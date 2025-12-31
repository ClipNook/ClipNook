<?php

declare(strict_types=1);

use App\Enums\ClipStatus;
use App\Models\Clip;
use App\Models\User;
use App\Repositories\ClipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ClipRepositoryTest', function () {
    it('can find clip by twitch id', function () {
        $clip = Clip::factory()->create(['twitch_clip_id' => 'test_clip_123']);

        $repository = new ClipRepository(new Clip);

        $foundClip = $repository->findByTwitchId('test_clip_123');

        expect($foundClip)->not->toBeNull();
        expect($foundClip->id)->toBe($clip->id);
    });

    it('can get clips by status', function () {
        Clip::factory()->count(2)->create(['status' => ClipStatus::APPROVED->value]);
        Clip::factory()->count(1)->create(['status' => ClipStatus::PENDING->value]);

        $repository = new ClipRepository(new Clip);

        $approvedClips = $repository->getByStatus(ClipStatus::APPROVED);
        $pendingClips  = $repository->getByStatus(ClipStatus::PENDING);

        expect($approvedClips)->toHaveCount(2);
        expect($pendingClips)->toHaveCount(1);
    });

    it('can get clips by submitter', function () {
        $user = User::factory()->create();
        Clip::factory()->count(3)->create(['submitter_id' => $user->id]);
        Clip::factory()->count(2)->create(); // Other user's clips

        $repository = new ClipRepository(new Clip);

        $userClips = $repository->getBySubmitter($user);

        expect($userClips)->toHaveCount(3);
        $userClips->each(function ($clip) use ($user) {
            expect($clip->submitter_id)->toBe($user->id);
        });
    });

    it('can get pending clips', function () {
        Clip::factory()->count(2)->create(['status' => ClipStatus::PENDING->value]);
        Clip::factory()->count(1)->create(['status' => ClipStatus::APPROVED->value]);

        $repository = new ClipRepository(new Clip);

        $pendingClips = $repository->getPending();

        expect($pendingClips)->toHaveCount(2);
        $pendingClips->each(function ($clip) {
            expect($clip->status)->toBe(ClipStatus::PENDING->value);
        });
    });

    it('can get approved clips', function () {
        Clip::factory()->count(3)->create(['status' => ClipStatus::APPROVED->value]);
        Clip::factory()->count(1)->create(['status' => ClipStatus::REJECTED->value]);

        $repository = new ClipRepository(new Clip);

        $approvedClips = $repository->getApproved();

        expect($approvedClips)->toHaveCount(3);
        $approvedClips->each(function ($clip) {
            expect($clip->status)->toBe(ClipStatus::APPROVED->value);
        });
    });

    it('can approve a clip', function () {
        $clip      = Clip::factory()->create(['status' => ClipStatus::PENDING->value]);
        $moderator = User::factory()->create();

        $repository = new ClipRepository(new Clip);

        $result = $repository->approveClip($clip, $moderator);

        expect($result)->toBeTrue();
        $clip->refresh();
        expect($clip->status)->toBe(ClipStatus::APPROVED->value);
        expect($clip->moderated_by)->toBe($moderator->id);
    });

    it('can reject a clip', function () {
        $clip      = Clip::factory()->create(['status' => ClipStatus::PENDING->value]);
        $moderator = User::factory()->create();

        $repository = new ClipRepository(new Clip);

        $result = $repository->rejectClip($clip, 'Not appropriate', $moderator);

        expect($result)->toBeTrue();
        $clip->refresh();
        expect($clip->status)->toBe(ClipStatus::REJECTED->value);
        expect($clip->moderation_reason)->toBe('Not appropriate');
        expect($clip->moderated_by)->toBe($moderator->id);
    });

    it('can get popular clips', function () {
        Clip::factory()->create(['status' => ClipStatus::APPROVED->value, 'view_count' => 100]);
        Clip::factory()->create(['status' => ClipStatus::APPROVED->value, 'view_count' => 200]);
        Clip::factory()->create(['status' => ClipStatus::APPROVED->value, 'view_count' => 50]);

        $repository = new ClipRepository(new Clip);

        $popularClips = $repository->getPopular(2);

        expect($popularClips)->toHaveCount(2);
        expect($popularClips->first()->view_count)->toBe(200);
        expect($popularClips->last()->view_count)->toBe(100);
    });

    it('can search clips', function () {
        Clip::factory()->create([
            'status'      => ClipStatus::APPROVED->value,
            'title'       => 'Funny moment',
            'description' => 'A great clip',
        ]);
        Clip::factory()->create([
            'status'      => ClipStatus::APPROVED->value,
            'title'       => 'Epic fail',
            'description' => 'Not funny',
        ]);

        $repository = new ClipRepository(new Clip);

        $results = $repository->search('funny');

        expect($results)->toHaveCount(2);
        expect($results->first()->title)->toBe('Funny moment');
    });

    it('can get clip statistics', function () {
        Clip::factory()->count(5)->create(['status' => ClipStatus::APPROVED->value]);
        Clip::factory()->count(3)->create(['status' => ClipStatus::PENDING->value]);
        Clip::factory()->count(2)->create(['status' => ClipStatus::REJECTED->value]);
        Clip::factory()->count(1)->create(['status' => ClipStatus::APPROVED->value, 'is_featured' => true]);

        $repository = new ClipRepository(new Clip);

        $stats = $repository->getStats();

        expect($stats['total'])->toBe(11);
        expect($stats['approved'])->toBe(6); // 5 + 1 featured
        expect($stats['pending'])->toBe(3);
        expect($stats['rejected'])->toBe(2);
        expect($stats['featured'])->toBe(1);
    });
});
