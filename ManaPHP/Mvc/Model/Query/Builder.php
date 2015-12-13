<?php 

namespace ManaPHP\Mvc\Model\Query {

	use ManaPHP\Di;
	use \ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Mvc\Model\Exception;
	use ManaPHP\Mvc\Model\Query;

	/**
	 * ManaPHP\Mvc\Model\Query\Builder
	 *
	 * Helps to create PHQL queries using an OO interface
	 *
	 *<code>
	 *$resultset = $this->modelsManager->createBuilder()
	 *   ->from('Robots')
	 *   ->join('RobotsParts')
	 *   ->limit(20)
	 *   ->orderBy('Robots.name')
	 *   ->getQuery()
	 *   ->execute();
	 *</code>
	 */
	
	class Builder implements BuilderInterface, InjectionAwareInterface {

		protected $_dependencyInjector;

		protected $_columns;

		protected $_models;

		protected $_joins;

		protected $_conditions;

		protected $_group;

		protected $_having;

		protected $_order;

		protected $_limit;

		protected $_offset;

		protected $_forUpdate;

		protected $_sharedLock;

		protected $_bindParams;

		protected $_bindTypes;

		protected $_distinct;

		protected $_hiddenParamNumber;

		protected $_lastSQL;

		/**
		 * @var boolean
		 */
		protected $_uniqueRow;
		/**
		 * \ManaPHP\Mvc\Model\Query\Builder constructor
		 *
		 *<code>
		 * $params = array(
		 *    'models'     => array('Users'),
		 *    'columns'    => array('id', 'name', 'status'),
		 *    'conditions' => array(
		 *        array(
		 *            "created > :min: AND created < :max:",
		 *            array("min" => '2013-01-01',   'max' => '2015-01-01'),
		 *            array("min" => PDO::PARAM_STR, 'max' => PDO::PARAM_STR),
		 *        ),
		 *    ),
		 *    // or 'conditions' => "created > '2013-01-01' AND created < '2015-01-01'",
		 *    'group'      => array('id', 'name'),
		 *    'having'     => "name = 'Kamil'",
		 *    'order'      => array('name', 'id'),
		 *    'limit'      => 20,
		 *    'offset'     => 20,
		 *    // or 'limit' => array(20, 20),
		 *);
		 *$queryBuilder = new \ManaPHP\Mvc\Model\Query\Builder($params);
		 *</code> 
		 *
		 * @param array|string $params
		 * @param \ManaPHP\Di $dependencyInjector
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function __construct($params=null, $dependencyInjector=null){
			if(is_array($params)){
				if(isset($params[0])){
					$this->_conditions =$params[0];
				}else{
					if(isset($params['conditions'])){
						$this->_conditions =$params['conditions'];
					}
				}

				if(is_array($this->_conditions)){
					$mergedConditions=[];
					$mergedParams=[];
					$mergedTypes=[];

					foreach($this->_conditions as $condition){
						if(is_array($condition)){
							if(is_string($condition[0])){
								$mergedConditions[]=$condition[0];
							}

							if(is_array($condition[1])){
								/** @noinspection SlowArrayOperationsInLoopInspection */
								$mergedParams=array_merge($mergedParams,$condition[1]);
							}

							if(is_array($condition[2])){
								/** @noinspection SlowArrayOperationsInLoopInspection */
								$mergedTypes =array_merge($mergedTypes,$condition[2]);
							}
						}
					}

					$this->_conditions=implode(' AND ',$mergedConditions);

					$this->_bindParams=$mergedParams;
					$this->_bindTypes =$mergedTypes;
				}

				if(isset($params['bind'])){
					$this->_bindParams=$params['bind'];
				}

				if(isset($params['distinct'])){
					$this->_distinct =$params['distinct'];
				}

				if(isset($params['models'])){
					$this->_models=$params['models'];
				}

				if(isset($params['columns'])){
					$this->_columns =$params['columns'];
				}

				if(isset($params['joins'])){
					$this->_joins=$params['joins'];
				}

				if(isset($params['group'])){
					$this->_group=$params['group'];
				}

				if(isset($params['having'])){
					$this->_having =$params['having'];
				}

				if(isset($params['order'])){
					$this->_order =$params['order'];
				}

				if(isset($params['limit'])){
					if(is_array($params['limit'])){
						throw new Exception('limit not support array format: '.$params['limit']);
					}else{
						$this->_limit =$params['limit'];
					}
				}

				if(isset($params['offset'])){
					$this->_offset=$params['offset'];
				}

				if(isset($params['for_update'])){
					$this->_forUpdate =$params['for_update'];
				}

				if(isset($params['shared_lock'])){
					$this->_sharedLock =$params['shared_lock'];
				}
			}else{
				if(is_string($params) && $params !==''){
					$this->_conditions=$params;
				}
			}

			if($dependencyInjector !==null){
				$this->_dependencyInjector =$dependencyInjector;
			}
		}


		/**
		 * Sets SELECT DISTINCT / SELECT ALL flag
		 *
		 * @param bool|null distinct
		 * @return static
		 */
		public function distinct($distinct){
			$this->_distinct =$distinct;
			return $this;
		}


		/**
		 * Returns SELECT DISTINCT / SELECT ALL flag
		 *
		 * @return bool
		 */
		public function getDistinct(){
			return $this->_distinct;
		}


		/**
		 * Sets the DependencyInjector container
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return static
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
			return $this;
		}


		/**
		 * Returns the DependencyInjector container
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}


		/**
		 * Sets the columns to be queried
		 *
		 *<code>
		 *	$builder->columns(array('id', 'name'));
		 *</code>
		 *
		 * @param string|array $columns
		 * @return static
		 */
		public function columns($columns){
			$this->_columns =$columns;
			return $this;
		}


		/**
		 * Return the columns to be queried
		 *
		 * @return string|array
		 */
		public function getColumns(){
			return $this->_columns;
		}


		/**
		 * Sets the models who makes part of the query
		 *
		 *<code>
		 *	$builder->from('Robots');
		 *	$builder->from(array('Robots', 'RobotsParts'));
		 *</code>
		 *
		 * @param string|array $models
		 * @return static
		 */
		public function from($models){
			$this->_models =$models;
			return $this;
		}


		/**
		 * Add a model to take part of the query
		 *
		 *<code>
		 *	$builder->addFrom('Robots', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $alias
		 * @return static
		 */
		public function addFrom($model, $alias=null){
			if(!is_array($this->_models)){
				if($this->_models !==null){
					$this->_models=[$this->_models];
				}else{
					$this->_models=[];
				}
			}

			if(is_string($alias)){
				$this->_models[$alias]=$model;
			}else{
				$this->_models[]=$model;
			}

			return $this;
		}


		/**
		 * Return the models who makes part of the query
		 *
		 * @return string|array
		 */
		public function getFrom(){
			return $this->_models;
		}


		/**
		 * Adds a join to the query
		 *
		 *<code>
		 *	$builder->join('Robots');
		 *	$builder->join('Robots', 'r.id = RobotsParts.robots_id');
		 *	$builder->join('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *	$builder->join('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @param string $type
		 * @return static
		 */
		public function join($model, $conditions=null, $alias=null, $type=null){
			$this->_joins[]=[$model,$conditions,$alias,$type];
			return $this;
		}


		/**
		 * Adds a INNER join to the query
		 *
		 *<code>
		 *	$builder->innerJoin('Robots');
		 *	$builder->innerJoin('Robots', 'r.id = RobotsParts.robots_id');
		 *	$builder->innerJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function innerJoin($model, $conditions=null, $alias=null){
			$this->_joins[]=[$model, $conditions,$alias,'INNER'];
			return $this;
		}


		/**
		 * Adds a LEFT join to the query
		 *
		 *<code>
		 *	$builder->leftJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function leftJoin($model, $conditions=null, $alias=null){
			$this->_joins[]=[$model, $conditions,$alias,'LEFT'];
			return $this;
		}


		/**
		 * Adds a RIGHT join to the query
		 *
		 *<code>
		 *	$builder->rightJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return static
		 */
		public function rightJoin($model, $conditions=null, $alias=null){
			$this->_joins[]=[$model, $conditions,$alias,'RIGHT'];
			return $this;
		}


		/**
		 * Sets the query conditions
		 *
		 *<code>
		 *	$builder->where('name = "Peter"');
		 *	$builder->where('name = :name: AND id > :id:', array('name' => 'Peter', 'id' => 100));
		 *</code>
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function where($conditions, $bindParams=null, $bindTypes=null){
			$this->_conditions =$conditions;

			if($bindParams !==null){
				if($this->_bindParams ===null){
					$this->_bindParams=$bindParams;
				}else{
					$this->_bindParams=array_merge($this->_bindParams,$bindParams);
				}
			}

			if($bindTypes !==null){
				if($this->_bindTypes ===null){
					$this->_bindTypes=$bindTypes;
				}else{
					$this->_bindTypes=array_merge($this->_bindTypes, $bindTypes);
				}
			}

			return $this;
		}


		/**
		 * Appends a condition to the current conditions using a AND operator
		 *
		 *<code>
		 *	$builder->andWhere('name = "Peter"');
		 *	$builder->andWhere('name = :name: AND id > :id:', array('name' => 'Peter', 'id' => 100));
		 *</code>
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function andWhere($conditions, $bindParams=null, $bindTypes=null){
			if(isset($this->_conditions)){
				$this->_conditions ='(' .$this->_conditions .') AND ('.$conditions.')';
			}else{
				$this->_conditions =$conditions;
			}

			if($bindParams !==null){
				if($this->_bindParams ===null){
					$this->_bindParams=$bindParams;
				}else{
					$this->_bindParams=array_merge($this->_bindParams,$bindParams);
				}
			}

			if($bindTypes !==null){
				if($this->_bindTypes ===null){
					$this->_bindTypes=$bindTypes;
				}else{
					$this->_bindTypes=array_merge($this->_bindTypes, $bindTypes);
				}
			}

			return $this;
		}


		/**
		 * Appends a condition to the current conditions using a OR operator
		 *
		 *<code>
		 *	$builder->orWhere('name = "Peter"');
		 *	$builder->orWhere('name = :name: AND id > :id:', array('name' => 'Peter', 'id' => 100));
		 *</code>
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return static
		 */
		public function orWhere($conditions, $bindParams=null, $bindTypes=null){
			if(isset($this->_conditions)){
				$this->_conditions ='(' .$this->_conditions .') OR ('.$conditions.')';
			}else{
				$this->_conditions =$conditions;
			}

			if($bindParams !==null){
				if($this->_bindParams ===null){
					$this->_bindParams=$bindParams;
				}else{
					$this->_bindParams=array_merge($this->_bindParams,$bindParams);
				}
			}

			if($bindTypes !==null){
				if($this->_bindTypes ===null){
					$this->_bindTypes=$bindTypes;
				}else{
					$this->_bindTypes=array_merge($this->_bindTypes, $bindTypes);
				}
			}

			return $this;
		}


		/**
		 * Appends a BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$builder->betweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return static
		 */
		public function betweenWhere($expr, $minimum, $maximum){
			$min_key ='ABP'.$this->_hiddenParamNumber++;
			$max_key ='ABP'.$this->_hiddenParamNumber++;

			$this->andWhere($expr. ' BETWEEN :'.$min_key. ': AND :'.$max_key.':',
					[$min_key=>$minimum,$max_key=>$maximum]);

			return $this;
		}


		/**
		 * Appends a NOT BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$builder->notBetweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return static
		 */
		public function notBetweenWhere($expr, $minimum, $maximum){
			$min_key ='ABP'.$this->_hiddenParamNumber++;
			$max_key ='ABP'.$this->_hiddenParamNumber++;

			$this->andWhere($expr. ' NOT BETWEEN :'.$min_key. ': AND :'.$max_key.':',
				[$min_key=>$minimum,$max_key=>$maximum]);

			return $this;
		}


		/**
		 * Appends an IN condition to the current conditions
		 *
		 *<code>
		 *	$builder->inWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return static
		 */
		public function inWhere($expr, $values){
			if(count($values) ===0){
				$this->andWhere($expr .' != '.$expr);
				return $this;
			}

			$bindParams =[];
			$bindKeys=[];

			foreach($values as $value){
				$key ='ABP'.$this->_hiddenParamNumber++;
				$bindKeys[]=':'.$key.':';
				$bindParams[$key]=$value;
			}
			$this->andWhere($expr.' IN ('.implode(', ',$bindKeys).')',$bindParams);

			return $this;
		}


		/**
		 * Appends a NOT IN condition to the current conditions
		 *
		 *<code>
		 *	$builder->notInWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return static
		 */
		public function notInWhere($expr, $values){
			if(count($values) ===0){
				$this->andWhere($expr .' != '.$expr);
				return $this;
			}

			$bindParams =[];
			$bindKeys=[];

			foreach($values as $value){
				$key ='ABP'.$this->_hiddenParamNumber++;
				$bindKeys[]=':'.$key.':';
				$bindParams[$key]=$value;
			}
			$this->andWhere($expr.' NOT IN ('.implode(', ',$bindKeys).')',$bindParams);

			return $this;
		}


		/**
		 * Return the conditions for the query
		 *
		 * @return string|array
		 */
		public function getWhere(){
			return $this->_conditions;
		}


		/**
		 * Sets a ORDER BY condition clause
		 *
		 *<code>
		 *	$builder->orderBy('Robots.name');
		 *	$builder->orderBy(array('1', 'Robots.name'));
		 *</code>
		 *
		 * @param string $orderBy
		 * @return static
		 */
		public function orderBy($orderBy){
			$this->_order =$orderBy;
			return $this;
		}


		/**
		 * Returns the set ORDER BY clause
		 *
		 * @return string|array
		 */
		public function getOrderBy(){
			return $this->_order;
		}


		/**
		 * Sets a HAVING condition clause. You need to escape PHQL reserved words using [ and ] delimiters
		 *
		 *<code>
		 *	$builder->having('SUM(Robots.price) > 0');
		 *</code>
		 *
		 * @param string $having
		 * @return static
		 */
		public function having($having){
			$this->_having =$having;
			return $this;
		}


		/**
		 * Return the current having clause
		 *
		 * @return string|array
		 */
		public function getHaving(){
			return $this->_having;
		}


		/**
		 * Sets a LIMIT clause, optionally a offset clause
		 *
		 *<code>
		 *	$builder->limit(100);
		 *	$builder->limit(100, 20);
		 *</code>
		 *
		 * @param int $limit
		 * @param int $offset
		 * @return static
		 */
		public function limit($limit, $offset=null){
			$this->_limit =$limit;
			if(isset($offset)){
				$this->_offset =$offset;
			}

			return $this;
		}


		/**
		 * Returns the current LIMIT clause
		 *
		 * @return string|array
		 */
		public function getLimit(){
			return $this->_limit;
		}


		/**
		 * Sets an OFFSET clause
		 *
		 *<code>
		 *	$builder->offset(30);
		 *</code>
		 *
		 * @param int $offset
		 * @return static
		 */
		public function offset($offset){
			$this->_offset =$offset;
			return $this;
		}


		/**
		 * Returns the current OFFSET clause
		 *
		 * @return string|null
		 */
		public function getOffset(){
			return $this->_offset;
		}


		/**
		 * Sets a GROUP BY clause
		 *
		 *<code>
		 *	$builder->groupBy(array('Robots.name'));
		 *</code>
		 *
		 * @param string $group
		 * @return static
		 */
		public function groupBy($group){
			$this->_group=$group;
			return $this;
		}


		/**
		 * Returns the GROUP BY clause
		 *
		 * @return string
		 */
		public function getGroupBy(){
			return $this->_group;
		}


		/**
		 * Returns a PHQL statement built based on the builder parameters
		 *
		 * @return string
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getPhql(){
			if($this->_dependencyInjector ===null){
				$dependencyInjector=Di::getDefault();
			}else{
				$dependencyInjector=$this->_dependencyInjector;
			}

			if($this->_models ===null){
				throw new Exception('At least one model is required to build the query');
			}else{
				if(count($this->_models) ===0){
					throw new Exception('At least one model is required to build the query');
				}
			}

			$conditions=$this->_conditions;

			/**
			 * Generate PHQL for SELECT
			 */
			if($this->_distinct !==null && is_bool($this->_distinct)){
				if($this->_distinct){
					$sql='SELECT DISTINCT ';
				}else{
					$sql='SELECT ALL ';
				}
			}else{
				$sql ='SELECT ';
			}

			/**
			 * Generate PHQL for columns
			 */
			if($this->_columns !==null){
				if(is_array($this->_columns)){
					$selectedColumns=[];
					foreach($this->_columns as $key=>$column){
						if(is_int($key)){
							$selectedColumns[]=$column;
						}else{

							if(strpos($key,'[') !==false){
								$selectedColumns[]= $column. ' AS '.$key;
							}else{
								$selectedColumns[]=$column. ' AS ['.$key.']';
							}
						}
					}
					$sql .=implode(', ',$selectedColumns);
				}else{
					$sql .=$this->_columns;
				}
			}else{
				if(is_array($this->_models)){
					$selectedColumns=[];
					foreach($this->_models as $alias=>$model){
						if(is_int($alias)){
							$selectedColumns[]='['.$model.'].*';
						}else{
							$selectedColumns[]='['.$alias.'].*';
						}
					}
					$sql .=implode(', ',$selectedColumns);
				}else{
					$sql .='['.$this->_models.'].*';
				}
			}

			/**
			 *  Join multiple models or use a single one if it is a string
			 */
			if(is_array($this->_models)){
				$selectedModels=[];
				foreach($this->_models as $alias=>$model){
					if(is_string($alias)){
						if(strpos($model, '[') !==false){
							$selectedModels[]=$model. ' AS ['.$alias.']';
						}else{
							$selectedModels[]='['.$model.'] AS ['.$alias.']';
						}
					}else{
						if(strpos($model,'[') !==false){
							$selectedModels[]=$model;
						}else{
							$selectedModels[]='['.$model.']';
						}
					}
				}
				$sql .=' FROM '.implode(', ',$selectedModels);
			}else{
				if(strpos($this->_models,'[') !==false){
					$sql .=' FROM '.$this->_models;
				}else{
					$sql.=' FROM [' .$this->_models.']';
				}
			}

			if(is_array($this->_joins)){
				foreach($this->_joins as $join){
					list($joinModel, $joinCondition, $joinAlias, $joinType)=$join;
					if($joinType){
						if(strpos($joinModel,'[') !==false){
							$sql .=' '.$joinType .' JOIN '.$joinModel;
						}else{
							$sql.=' '.$joinType.' JOIN ['.$joinModel.']';
						}
					}else{
						if(strpos($joinModel,']') !==false){
							$sql .=' JOIN '.$joinModel;
						}else{
							$sql.=' JOIN ['.$joinModel.']';
						}
					}

					if($joinAlias){
						$sql .=' AS ['.$joinAlias.']';
					}

					if($joinCondition){
						$sql .=' ON '.$joinCondition;
					}
				}
			}

			if(is_string($conditions) && $conditions !==''){
				$sql .=' WHERE '.$conditions;
			}

			/**
			 * Process group parameters
			 * todo
			 */

			/**
			 * Process group parameters
			 * todo
			 */

			/**
			 * Process order clause
			 * todo
			 */
			if($this->_order !==null){
				if(is_array($this->_order)){
					$orderItems=[];

					foreach($this->_order as $item){
						if(strpos($item,'.')){
							$orderItems[]=$item;
						}else{
							$orderItems[]='['.$item.']';
						}
					}
					$sql .=' ORDER BY '.implode(', ',$orderItems);
				}else{
					$sql .=' ORDER BY '.$this->_order;
				}
			}

			/**
			 * Process limit parameters
			 * todo
			 */
			if($this->_limit !==null){
				$key ='ABP_LIMIT';
				$sql .=' LIMIT :'.$key.':';
				$this->_bindParams[$key]=(int)$this->_limit;
				//$this->_bindTypes[$key]=\PDO::PARAM_INT;
			}

			if($this->_offset !==null){
				if($this->_limit ===null){
					throw new Exception('offset is invalid: limit is missing');
				}
				$key ='ABP_OFFSET';
				$sql .=' OFFSET :'.$key.':';
				$this->_bindParams[$key]=(int)$this->_offset;
				$this->_bindTypes[$key]=\PDO::PARAM_INT;
			}
			/**
			 * Process forUPDATE clause
			 * todo
			 */

			return $sql;

		}


		/**
		 * Returns the query built
		 *
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getQuery(){
			$this->_lastSQL=$this->getPhql();
			return $this;
		}

		/**
		 * Tells to the query if only the first row in the resultset must be returned
		 * @param boolean $uniqueRow
		 * @return static
		 */
		public function setUniqueRow($uniqueRow)
		{
			$this->_uniqueRow = $uniqueRow;
			return $this;
		}
		/**
		 * Executes a parsed PHQL statement
		 *
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return mixed
		 * @throws \ManaPHP\Mvc\Model\Exception|\ManaPHP\Di\Exception
		 */
		public function execute($bindParams=null, $bindTypes=null){
//			$query = new Query($this->getPhql(),$this->_dependencyInjector);
//			if(is_array($this->_bindParams)){
//				$query->setBindParams($this->_bindParams);
//			}
//
//			if(is_array($this->_bindTypes)){
//				$query->setBindTypes($this->_bindTypes);
//			}

			if($bindParams !==null &&is_array($bindParams)){
				$mergedParams=array_merge($this->_bindParams,$bindParams);
			} else{
				$mergedParams=$this->_bindParams;
			}

			if($bindTypes !==null &&is_array($bindTypes)){
				$mergedTypes=array_merge($this->_bindTypes, $bindTypes);
			}else{
				$mergedTypes=$this->_bindTypes;
			}

			$sql=$this->_lastSQL;
			/**
			 * @var \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
			 */
			$modelsManager =$this->_dependencyInjector->getShared('modelsManager');

			if(is_string($this->_models)){
				$models=[$this->_models];
			}else{
				$models=$this->_models;
			}
			foreach($models as $model){
				$modelInstance=$modelsManager->load($model,false);

				$schema =$modelInstance->getSchema();
				$source =$modelInstance->getSource();
				if($schema){
					$table=[$schema,$source];
				}else{
					$table=$source;
				}
				$readConnection=$modelInstance->getReadConnection();
				$escapedTable=$readConnection->escapeIdentifier($table);
				$sql =str_replace('['.$model.']',$escapedTable,$sql);
			}

			/** @noinspection StrTrUsageAsStrReplaceInspection */
			$sql=strtr($sql,'[]','``');

			if(is_array($mergedParams)){
				$sql_replaces=[];
				$finalBindParams=[];
				foreach($mergedParams as $key=>$value){
					$sql_replaces[':'.$key.':']=':'.$key;
					$finalBindParams[':'.$key]=$value;
				}

				$sql =strtr($sql,$sql_replaces);
			}else{
				$finalBindParams=null;
			}
			try{
				if($this->_uniqueRow){
					$result=$readConnection->fetchOne($sql,\PDO::FETCH_ASSOC,$finalBindParams,$mergedTypes);
				}else{
					$result =$readConnection->fetchAll($sql,\PDO::FETCH_ASSOC,$finalBindParams,$mergedTypes);
				}

			}catch (\Exception $e){
				throw new Exception($e->getMessage().':'.$sql);
			}

			return $result;
		}

		/**
		 * Set default bind parameters
		 *
		 * @param array $bindParams
		 * @param bool $merge
		 * @return static
		 */
		public function setBindParams($bindParams, $merge = false){
			if($merge ===false){
				$this->_bindParams=$bindParams;
			}else{
				$this->_bindParams=array_merge($this->_bindParams,$bindParams);
			}

			return $this;
		}

	}
}
