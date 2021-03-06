<?php
namespace ManaPHP\Authentication {

    interface PasswordInterface
    {
        /**
         * generate a salt
         *
         * @param int $length
         *
         * @return string
         */
        public function salt($length = 8);

        /**
         * @param string $pwd
         * @param string $salt
         *
         * @return mixed
         */
        public function hash($pwd, $salt = null);

        /**
         * @param  string $pwd
         * @param  string $hash
         * @param  string $salt
         *
         * @return bool
         */
        public function verify($pwd, $hash, $salt = null);
    }
}