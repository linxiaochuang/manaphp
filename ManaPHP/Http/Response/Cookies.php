<?php 

namespace ManaPHP\Http\Response {

	/**
	 * ManaPHP\Http\Response\Cookies
	 *
	 * This class is a bag to manage the cookies
	 * A cookies bag is automatically registered as part of the 'response' service in the DI
	 */
	
	class Cookies implements \ManaPHP\Http\Response\CookiesInterface, \ManaPHP\Di\InjectionAwareInterface {

		/**
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector;

		protected $_registered;

		protected $_useEncryption;

		/**
		 * @var \ManaPHP\Http\Cookie[]
		 */
		protected $_cookies;

		/**
		 * Sets the dependency injector
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
		}


		/**
		 * Returns the internal dependency injector
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}


		/**
		 * Set if cookies in the bag must be automatically encrypted/decrypted
		 *
		 * @param boolean $useEncryption
		 * @return \ManaPHP\Http\Response\Cookies
		 */
		public function useEncryption($useEncryption){
			$this->_useEncryption =$useEncryption;
		}


		/**
		 * Returns if the bag is automatically encrypting/decrypting cookies
		 *
		 * @return boolean
		 */
		public function isUsingEncryption(){
			return $this->_useEncryption;
		}


		/**
		 * Sets a cookie to be sent at the end of the request
		 * This method overrides any cookie set before with the same name
		 *
		 * @param string $name
		 * @param mixed $value
		 * @param int $expire
		 * @param string $path
		 * @param boolean $secure
		 * @param string $domain
		 * @param boolean $httpOnly
		 * @return \ManaPHP\Http\Response\Cookies
		 * @throws
		 */
		public function set($name, $value=null, $expire=null, $path=null, $secure=null, $domain=null, $httpOnly=null){
			/**
			 * @var \ManaPHP\Http\Cookie $cookie
			 * @var \ManaPHP\Http\ResponseInterface $response
			 */
			if(!isset($this->_cookies[$name])){

				$cookie =$this->_dependencyInjector->get('ManaPHP\Http\Cookie',
							[$name,$value,$expire,$path,$secure,$domain,$httpOnly]);

				$cookie->setDI($this->_dependencyInjector);
				$cookie->useEncryption($this->_useEncryption);
				$this->_cookies[$name]=$cookie;
			}else{
				$cookie =$this->_cookies[$name];

				$cookie->setValue($value);
				$cookie->setExpiration($expire);
				$cookie->setPath($path);
				$cookie->setSecure($secure);
				$cookie->setDomain($domain);
				$cookie->setHttpOnly($httpOnly);
			}

			if($this->_registered ===false){
				if(!is_object($this->_dependencyInjector)){
					throw new Exception("A dependency injection object is required to access the 'response' service");
				}

				$response =$this->_dependencyInjector->getShared('response');
				$response->setCookies($this);
			}

			return $this;
		}


		/**
		 * Gets a cookie from the bag
		 *
		 * @param string $name
		 * @return \ManaPHP\Http\Cookie
		 */
		public function get($name){
			/**
			 * @var \ManaPHP\Http\Cookie $cookie
			 */
			if(isset($this->_cookies[$name])){
				return $this->_cookies[$name];
			}


			$cookie =$this->_dependencyInjector->get('ManaPHP\Http\Cookie',[$name]);
			if(is_object($this->_dependencyInjector)){
				$cookie->setDI($this->_dependencyInjector);
				$cookie->useEncryption($this->_useEncryption);
			}
			$this->_cookies[$name] =$cookie;

			return $cookie;
		}


		/**
		 * Check if a cookie is defined in the bag or exists in the $_COOKIE superglobal
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function has($name){
			if(isset($this->_cookies[$name])){
				return true;
			}

			if(isset($_COOKIE[$name])){
				return true;
			}

			return false;
		}


		/**
		 * Deletes a cookie by its name
		 * This method does not removes cookies from the $_COOKIE superglobal
		 *
		 * @param string $name
		 * @return boolean
		 */
		public function delete($name){
			if(isset($this->_cookies[$name])){
				$this->_cookies[$name]->delete();
				return true;
			}else{
				return false;
			}
		}


		/**
		 * Sends the cookies to the client
		 * Cookies aren't sent if headers are sent in the current request
		 *
		 * @return boolean
		 */
		public function send(){
			if(!headers_sent()){
				foreach($this->_cookies as $cookie){
					$cookie->send();
				}

				return true;
			}else{
				return false;
			}
		}


		/**
		 * Reset set cookies
		 *
		 * @return \ManaPHP\Http\Response\Cookies
		 */
		public function reset(){
			$this->_cookies =[];
			return $this;
		}
	}
}