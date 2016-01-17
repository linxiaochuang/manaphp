<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2016/1/16
 * Time: 8:38
 */
namespace ManaPHP\Mvc\Dispatcher {

    use ManaPHP\Di\Injectable;
    use ManaPHP\Events\ListenerInterface;

    class Listener extends Injectable implements ListenerInterface
    {
        /**
         * @param \ManaPHP\Events\Event $event
         * @param \ManaPHP\Mvc\Dispatcher $dispatcher
         * @return bool
         */
        public function beforeDispatchLoop($event, $dispatcher)
        {
            return true;
        }

        /**
         * @param \ManaPHP\Events\Event $event
         * @param \ManaPHP\Mvc\Dispatcher $dispatcher
         * @return bool
         */
        public function beforeDispatch($event, $dispatcher)
        {
            return true;
        }

        /**
         * @param \ManaPHP\Events\Event $event
         * @param \ManaPHP\Mvc\Dispatcher $dispatcher
         * @return void
         */
        public function afterDispatch($event, $dispatcher)
        {
        }
    }
}