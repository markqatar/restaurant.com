<?php
class HelloWorld {
    /**
     * @url GET /hello
     */
    public function sayHello() {
        return ['message' => 'Hello from Restler!'];
    }
}