<?php
namespace ManaPHP\Mvc {

    interface UrlInterface
    {
        /**
         * Sets a base uri to all the urls generated
         *
         * @param string $baseUri
         *
         * @return static
         */
        public function setBaseUri($baseUri);

        /**
         * Returns the base uri for all the generated urls.
         */
        public function getBaseUri();

        /**
         * @param string $uri
         * @param array  $args
         *
         * @return mixed
         */
        public function get($uri = null, $args = null);

        /**
         * @param string      $uri
         * @param bool|string $correspondingMin
         *
         * @return string
         */
        public function getCss($uri, $correspondingMin = true);

        /**
         * @param string      $uri
         * @param bool|string $correspondingMin
         *
         * @return string
         */
        public function getJs($uri, $correspondingMin = true);
    }
}