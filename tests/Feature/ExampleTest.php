<?php

it('takes guests to the administration login', function () {
    $this->get('/')->assertRedirect('/admin/login');
    $this->get('/admin/login')->assertOk();
});
