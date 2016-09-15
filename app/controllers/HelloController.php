<?php

class HelloController extends \BaseController {

    /**
     * Display a listing of the resource.
     *
     *
     * @return Response
     */
    public function index() {
        echo distanceGeoPoints(22, 50.0000001, 22, 50.000001);
    }

    public function test() {
        test_ios_noti("357eacdb0f2c1fb2e0196f41282b8b9f6e9fbd83ced7e0cf3d1cb68e732ba599", "provider", "my title", "my_message");
        test_ios_noti("77C892043F137BC4A8AF2A324BF73AD9C6B7859F7CC3116E837BAA2EC4731666", "user", "my title", "my_message");
    }

    public function test_and() {
        send_notifications(8, "provider", "my title-provider", "my_message");
        send_notifications(15, "user", "my title-client", "my_message");
    }

}
