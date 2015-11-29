<?php 

namespace ManaPHP\Events {

	/**
	 * ManaPHP\Events\Manager
	 *
	 * ManaPHP Events Manager, offers an easy way to intercept and manipulate, if needed,
	 * the normal flow of operation. With the EventsManager the developer can create hooks or
	 * plugins that will offer monitoring of data, manipulation, conditional execution and much more.
	 *
	 */
	
	class Manager implements \ManaPHP\Events\ManagerInterface {

		/**
		 * @var array
		 */
		protected $_events=[];

		/**
		 * Attach a listener to the events manager
		 *
		 * @param string $event
		 * @param object|callable $handler
		 * @throws \ManaPHP\Events\Exception
		 */
		public function attach($event, $handler){
			if(!is_object($handler)){
				throw new Exception("Event handler must be an Object");
			}

			if(strpos($event,':') !==false) {
				list($type,$name)=explode(':',$event);
			}else{
				$type=$event;
				$name='';
			}

			if(!isset($this->_events[$type])){
				$this->_events[$type]=[];
			}

			$this->_events[$type][] =['event'=>$event,
											'name'=>$name,
											'handler'=>$handler];
		}

		/**
		 * Fires an event in the events manager causing that active listeners be notified about it
		 *
		 *<code>
		 *	$eventsManager->fire('db', $connection);
		 *</code>
		 *
		 * @param string $event
		 * @param object $source
		 * @param mixed  $data
		 * @throws \ManaPHP\Events\Exception
		 */
		public function fire($event, $source, $data=null){
			if(strpos($event,':') ===false){
				throw new Exception("Invalid event type " . $event);
			}

			list($fire_type,$fire_name)=explode(':',$event,2);

			if(isset($this->_events[$fire_type])){
				$callback_params=[new Event($fire_type), $source, $data];

				foreach($this->_events[$fire_type] as $event_handler){
					$name =$event_handler['name'];

					if($name===''||$name===$fire_name){
						$handler=$event_handler['handler'];

						$callback =null;
						if($handler instanceof \Closure){
							$callback=$handler;
						}else{
							if(method_exists($handler,$name)){
								$callback=[$handler,$name];
							}
						}
						
						if($callback !==null){
							call_user_func_array($callback,$callback_params);
						}
					}
				}
			}
		}
	}
}