<?php

it('renders language switcher and contains configured locales', function () {
    $response = $this->get('/');

    $response->assertStatus(200);

    foreach (array_keys(config('app.locales')) as $code) {
        $response->assertSee('/lang/'.$code);
    }
});

it('can set locale via ajax post', function () {
    $token = 'test_token';
    $this->withSession(['_token' => $token])->withHeader('X-CSRF-TOKEN', $token)->postJson('/lang', ['locale' => 'de'])->assertNoContent();
    $this->assertEquals('de', session('locale'));
});

it('returns 422 for invalid locale via ajax', function () {
    $token = 'test_token';
    $this->withSession(['_token' => $token])->withHeader('X-CSRF-TOKEN', $token)->postJson('/lang', ['locale' => 'xx'])->assertStatus(422);
});
