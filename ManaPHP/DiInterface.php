<?php 

namespace ManaPHP {

	/**
	 * ManaPHP\DiInterface initializer
	 */

	interface DiInterface extends \ArrayAccess {

		/**
		 * Registers a service in the service container
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
		 * @return \ManaPHP\Di\ServiceInterface
		 */
		public function set($name, $definition, $shared=false);


		/**
		 * Registers an "always shared" service in the services container
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @return \ManaPHP\Di\ServiceInterface
		 */
		public function setShared($name, $definition);


		/**
		 * Attempts to register a service in the services container
		 * Only is successful if a service hasn't been registered previously
		 * with the same name
		 *
		 * @param string $name
		 * @param mixed $definition
		 * @param boolean $shared
		 * @return \ManaPHP\Di\ServiceInterface
		 */
		public function attempt($name, $definition, $shared = false);


		/**
		 * Removes a service from the service container
		 *
		 * @param string $name
		 * @return void
		 */
		public function remove($name);


		/**
		 * Resolves the service based on its configuration
		 *
		 * @param string $name
		 * @param array $parameters
		 * @return mixed
		 */
		public function get($name, $parameters=null);


		/**
		 * Resolves a shared service based on their configuration
		 *
		 * @param string $name
		 * @param array $parameters
		 * @return mixed
		 */
		public function getShared($name, $parameters=null);


		/**
		 * Returns the corresponding \ManaPHP\Di\Service instance for a service
		 *
		 * @param string $name
		 * @return \ManaPHP\Di\ServiceInterface
		 */
		public function getService($name);


		/**
		 * Check whether the DI contains a service by a name
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function has($name);


		/**
		 * Check whether the last service obtained via getShared produced a fresh instance or an existing one
		 *
		 * @return boolean
		 */
		public function wasFreshInstance();


		/**
		 * Return the services registered in the DI
		 *
		 * @return array
		 */
		public function getServices();


		/**
		 * Set the default dependency injection container to be obtained into static methods
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public static function setDefault($dependencyInjector);


		/**
		 * Return the last DI created
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public static function getDefault();


		/**
		 * Resets the internal default DI
		 */
		public static function reset();

	}
}