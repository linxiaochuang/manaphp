<?php
namespace ManaPHP {

    use ManaPHP\Serializer\Adapter\JsonPhp;

    class Store implements StoreInterface
    {
        /**
         * @var \ManaPHP\Store\AdapterInterface
         */
        protected $_adapter;

        /**
         * @var string
         */
        protected $_prefix;

        /**
         * @var \ManaPHP\Serializer\AdapterInterface $_serializer
         */
        protected $_serializer;

        /**
         * Store constructor.
         * @param string $prefix
         * @param string|\ManaPHP\Store\AdapterInterface $adapter
         * @param \ManaPHP\Serializer\AdapterInterface $serializer
         * @throws \ManaPHP\Di\Exception
         */
        public function __construct($prefix = '',$adapter=null,$serializer = null)
        {
            $this->_prefix=$prefix;

            if($adapter===null){
                $this->_adapter=Di::getDefault()->getShared('defaultStoreAdapter');
            }elseif(is_string($adapter)){
                $this->_adapter=Di::getDefault()->getShared($adapter);
            }else{
                $this->_adapter = $adapter;
            }

            if ($serializer === null) {
                $this->_serializer = new JsonPhp();
            } else {
                $this->_serializer = $serializer;
            }
        }


        /**
         * Fetch content
         *
         * @param string $id
         * @return mixed
         * @throws \ManaPHP\Store\Exception
         */
        public function get($id)
        {
            $content = $this->_adapter->get($this->_prefix . $id);
            if ($content === false) {
                return false;
            }

            return $this->_serializer->deserialize($content);
        }

        /**
         * Retrieves a value from store with a specified id.
         *
         * @param array $ids
         * @return array
         * @throws \ManaPHP\Store\Exception
         */
        public function mGet($ids)
        {
            $completeIds = [];
            foreach ($ids as $id) {
                $completeIds[] = $this->_prefix . $id;
            }

            $completeIdValues = $this->_adapter->mGet($completeIds);
            $idValues = [];
            foreach ($completeIdValues as $completeId => $value) {
                $id = substr($completeId, strlen($this->_prefix));
                if ($value === false) {
                    $idValues[$id] = $value;
                } else {
                    $idValues[$id] = $this->_serializer->deserialize($value);
                }
            }

            return $idValues;
        }

        /**
         * Stores content
         * @param string $id
         * @param mixed $value
         * @return void
         * @throws \ManaPHP\Cache\Exception
         */
        public function set($id, $value)
        {
            $this->_adapter->set($this->_prefix . $id, $this->_serializer->serialize($value));
        }


        /**
         * Stores a value identified by a id into store.
         *
         * @param array $idValues
         * @return void
         */
        public function mSet($idValues)
        {
            $completeIdValues = [];
            foreach ($idValues as $id => $value) {
                $completeIdValues[$this->_prefix . $id] = $this->_serializer->serialize($value);
            }

            $this->_adapter->mSet($completeIdValues);
        }

        /**
         * Delete content
         *
         * @param string $id
         * @void
         */
        public function delete($id)
        {
            $this->_adapter->delete($this->_prefix . $id);
        }


        /**
         * Check if id exists
         *
         * @param string $id
         * @return bool
         */
        public function exists($id)
        {
            return $this->_adapter->exists($this->_prefix . $id);
        }

        /**
         * @return \ManaPHP\Store\AdapterInterface
         */
        public function getAdapter()
        {
            return $this->_adapter;
        }

        /**
         * @return \ManaPHP\Serializer\AdapterInterface
         */
        public function getSerializer()
        {
            return $this->_serializer;
        }

        /**
         * @return array
         */
        public function __debugInfo()
        {
            return get_object_vars($this);
        }
    }
}