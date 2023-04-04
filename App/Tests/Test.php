<?php

namespace App\Tests;

use App\Lib\DB;

class Test
{
    /**
     * It can be used for testing,
     * Url: /tests
     */
    public function index()
    {
        /**
         * Test the config function
         */
        if (0) {
            $displayErrors = config('app.displayErrors');
            dump($displayErrors);
        }

        /**
         * Test the data validator
         */
        if (0) {
            $data = [
                'name' => 'Ethan Cheng',
            ];

            $rules = [
                'name' => ['required'],
            ];

            $messages = [
                'name.required' => 'The name field is required.',
            ];
            $validator = new \App\Lib\Validator($data, $rules, $messages);
            $resultValidated = $validator->validate();
            if ($resultValidated !== true) {
                \App\Lib\ApiOutput::ApiOutput($resultValidated, 412);
            }
            echo 'moving on';
        }

        echo '<br>';
        echo '<br>';
        echo '<hr>';
        echo 'Testing End';
    }
}
