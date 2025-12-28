<?php

it('masks emails with given options', function () {
    expect(mask_email('a@b.c', ['mask_char' => '*', 'visible_local' => 0]))->toBe('*@*.c');

    // deterministic example
    expect(mask_email('user@example.com'))->toBe('u••r@e•••••e.com');

    // empty input
    expect(mask_email(null))->toBe('');
});

it('masks ipv4 and ipv6 addresses and handles null', function () {
    expect(mask_ip('192.168.1.55'))->toBe('192.168.1.0');

    $ipv6 = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
    expect(mask_ip($ipv6))->toBe('2001:0db8:85a3:0000::');

    expect(mask_ip(null))->toBeNull();
});

it('hashes user agents and returns null for empty', function () {
    $ua = 'Mozilla/5.0 (X11; Linux x86_64)';
    expect(hash_user_agent($ua))->toBe(hash('sha256', $ua));
    expect(hash_user_agent(null))->toBeNull();
});
