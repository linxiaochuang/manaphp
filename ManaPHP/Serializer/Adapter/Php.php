<?php
namespace ManaPHP\Serializer\Adapter {

    use ManaPHP\Serializer\AdapterInterface;
    use ManaPHP\Serializer\Exception;

    class Php implements AdapterInterface
    {
        public function serialize($data, $context = null)
        {
            if (is_array($data)) {
                $packedData = $data;
            } else {
                $packedData = ['__wrapper__' => $data];
            }

            return serialize($packedData);
        }

        public function deserialize($serialized, $content = null)
        {
            $data = unserialize($serialized);
            if ($data === false) {
                throw new Exception('unserialize failed: ' . error_get_last()['message']);
            }

            if (!is_array($data)) {
                throw new Exception('serialize serialized data has been corrupted.');
            }

            if (isset($data['__wrapper__']) && count($data) === 1) {
                return $data['__wrapper__'];
            } else {
                return $data;
            }
        }
    }
}