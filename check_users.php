<?php
$users = \DB::table('users')->select('id','name','email','email_verified_at')->get();
echo json_encode($users) . "\n";
echo 'Count: ' . $users->count() . "\n";
