<?php  namespace Glavred\Lib;

use \Exception;



/* -------------------  класс ---------------------------- */

    class TreeMaker{
        /**
         * лимит итерация хождения по ветке
         */
        public $iterLimit = 10000;
        
        private $raw;                   // Сырые данные
        private $inpArr;                // Сырые данные + paths
        private $rawKeyId = [];         // Ключи ==  ID
        private $config = [];           // настройки
        

        /**
         * Устанавливает настройки
         *
         * @param $raw сырой массив + ключи доступа
         * @return void
         */

        public function __construct($raw, $pKey = 'parent',$pId = 'id', 
                $childsName = 'childs' , 
                $level = 'level'){
           
                $this->inpArr = $this->raw = $raw;
                $this->inpArr = json_decode(json_encode($raw), true);

                if(!is_array($this->inpArr)){
                    throw new Exception('Input data must be an array');
                }
               
               $this->config = [
                    'pid'     => $pKey,
                    'id'      => $pId,
                    'childs'  => $childsName,
                    'level'  => $level,
               ];
               
               //var_dump($this->inpArr);
               
               foreach($this->inpArr as $item){
                   $this->rawKeyId[$item[$this->config['id']]] = $item; 
               }
        }
        
       
      /**
       * Добавляет к каждому элементу путь до него,
       * если указан $separator вывод будет в формате строки.
       * Полезно для быстрого вывода пути.
       * Применение: дерево пусти каталогов
       * @param string $rowName имя соединяемого поля
       * @return $this
      */        
      public function addWay(string $rowName, string $forKey = '__path', $separator = false) 
      {
          foreach($this->inpArr as &$item) {
              $paths = $this->getWayById($rowName, $item[$this->config['id']], $separator);
              $item[$forKey] = is_string($separator) ? implode($separator, $paths) 
                                                  : $paths;   
          }      
          return $this;
      }      
        
        
      /**
       * Отдает обработнный массив формате дерева
       * @return array
      */         
      public function getTree() : array{
          return $this->makeTree();
      }      
        
        
      /**
       * Отдает обработнный массив  формате  листинга
       * @param bool $deleteChild нужно ли удалить дерево потомков
       * @return array
      */         
      public function getList(bool $deleteChild = true) : array{
          return $this->makeList($deleteChild);
      }             
        
        
        
        
        
        /**
         * Создает дерево из элементов (id, parent)
         *
         * @return []
         */       
        private function makeTree():array
        {
          $childs = [];
         
          foreach($this->inpArr as &$item) {
              $childs[$item[$this->config['pid']]][] = &$item;
          }
          
          //unset($item);
          foreach($this->inpArr as &$item){ 
              if (isset($childs[$item[$this->config['id']]])){
                    $item[$this->config['childs']] = $childs[$item[$this->config['id']]];
              }
          }

          return $childs[0];          
      }     

       /**
       * По id возращает массив пути до элемента.
       * 
       * @return [] 
       */   
       private function getWayById(string $rowName, int $id): array
       {
                    
            $parent = $this->rawKeyId[$id][$this->config['pid']];
            $output  = [$this->rawKeyId[$id][$rowName]];
            $limit = $this->iterLimit;
            $level = 0;
            while($parent != 0){
                    if(--$limit == 0) {throw new Exception('too many iteration');}
                    $level++;
                    $output[] = $this->rawKeyId[$parent][$rowName];
                    $parent = $this->rawKeyId[$parent][$this->config['pid']];
            }
            return array_reverse($output);
       }     
      
      
       /**
       * По id возращает вложенность.
       * 
       * @return integer
       */        
        private function getLevel($id): int
        {
            $parent = $this->rawKeyId[$id][$this->config['pid']];
            $limit = $this->iterLimit;
            $level = 0;
            while($parent != 0){
                    if(--$limit == 0) {throw new Exception('too many iteration');}
                    $level++;
                    $parent = $this->rawKeyId[$parent][$this->config['pid']];
            }
            
            return $level;
        }
      
       /**
       * Из массива с деревом наследником 
       * создает одноуровневый массив с указанием вложенности
       *
       * @return $this
       */       
       private function makeList($deleteChild) {
                      
           $output = [];
           $recursion = function($items) use (&$recursion, &$output){
               
               foreach($items as $item){
                       $output[] = $item;
                       $output[count($output)-1][$this->config['level']] = $this->getLevel($item[$this->config['id']]);
                       if(isset($item[$this->config['childs']])){
                           $recursion($item[$this->config['childs']]);
                       }
               }
               return $output; 
           };
           
           $list = $recursion($this->makeTree());
           if($deleteChild == true){
              foreach($list as &$item){
                  unset($item[$this->config['childs']]);
              }
           }
           return $list;
       }
      
       



    }



/* ------------------- // класс ---------------------------- */