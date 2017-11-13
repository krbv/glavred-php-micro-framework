<?php  namespace Glavred\Lib;

use \Exception;


/* -------------------  класс ---------------------------- */
    class ImageResizer{
        private $file;              //Путь к файлу с исходным изображением
        private $image;             //Исходное изображение
        private $imageResized;   //Изображение после масштабирования
        private $quality = 90;
        /**
         * Загрузка файла для обработки
         *
         * @param string $file путь к файлу
         */
        public function __construct($file){
            
            if(!file_exists($file)) {
                throw new Exception('File not found');
            }
            //Получаем информацию о файле
            list($width, $height, $image_type) = getimagesize($file);
     
            //Создаем изображение из файла
            switch ($image_type){
                case 1: $this->image = imagecreatefromgif($file);   break;
                case 2: $this->image = imagecreatefromjpeg($file);  break;
                case 3: $this->image = imagecreatefrompng($file);   break;
                default:  throw new  Exception("Сan't typify");
            }
            $this->file=$file;
            
            $this->imageResized = $this->image;
        }
		
		//HEX TO RGB
	public function hex2rgb($hex) {
		
		   $hex = str_replace("#", "", $hex);
		   if(strlen($hex) == 3) {
			  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
			  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
			  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
		   } else {
			  $r = hexdec(substr($hex,0,2));
			  $g = hexdec(substr($hex,2,2));
			  $b = hexdec(substr($hex,4,2));
		   }
		   return [$r, $g, $b]; 
	}
     
        /**
         * Масштабирует исходное изображение
         *
         * @param int $W Ширина
         * @param int $H Высота
         */
        public function resize($W, $H){
		            
            $X=ImageSX($this->imageResized);
            $Y=ImageSY($this->imageResized);
     
            $H_NEW=$Y;
            $W_NEW=$X;
     
            if($X>$W){
                $W_NEW=$W;
                $H_NEW=$W*$Y/$X;
            }
     
            if($H_NEW>$H){
                $H_NEW=$H;
                $W_NEW=$H*$X/$Y;
            }
     
            $H=(int)$H_NEW;
            $W=(int)$W_NEW;
     
     
            $substrate=imagecreatetruecolor($W, $H);
            imagecopyresampled($substrate, $this->imageResized,0,0,0,0,$W,$H,$X,$Y);
            
            return $this->imageResized = $substrate;
     
        }
        
    
        public function crop($thumb_width, $thumb_height){
            $width = imagesx($this->imageResized);
            $height = imagesy($this->imageResized);
            $original_aspect = $width / $height;
            $thumb_aspect = $thumb_width / $thumb_height;
            if ( $original_aspect >= $thumb_aspect )
            {
               // If image is wider than thumbnail (in aspect ratio sense)
               $new_height = $thumb_height;
               $new_width = $width / ($height / $thumb_height);
            }
            else
            {
               // If the thumbnail is wider than the image
               $new_width = $thumb_width;
               $new_height = $height / ($width / $thumb_width);
            }
            $substrate = imagecreatetruecolor( $thumb_width, $thumb_height );
            // Resize and crop
            imagecopyresampled($substrate,
                               $this->imageResized,
                               0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                               0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                               0, 0,
                               $new_width, $new_height,
                               $width, $height);
            
            return $this->imageResized = $substrate;
        }
        
	public function background($W, $H, $color = "#eee"){
			//make a substrate
			$rgb = $this->hex2rgb($color);
			$substrate  = imagecreatetruecolor($W, $H);
			$bgc = imagecolorallocate($substrate, $rgb[0], $rgb[1], $rgb[2]);
			imagefilledrectangle($substrate, 0, 0, $W, $H, $bgc);
			
			$wmW=imagesx($this->imageResized);
			$wmH=imagesy($this->imageResized);
			$ot_w = $ot_h =0;		 
			if ($wmH<$H){$ot_h=floor(($H-$wmH)/2);}
			if ($wmW<$W){$ot_w=floor(($W-$wmW)/2);}
			imagecopy ($substrate, $this->imageResized, $ot_w, $ot_h, 0, 0, $wmW, $wmH);
						
			return $this->imageResized = $substrate;			
	}
        
        public function blur($blurs = 5){
            for ($i = 0; $i < $blurs; $i++) {
                imagefilter($this->imageResized, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }
        
        //today midnight or 22.10.2100 or unixtime
        public function setExpires($date){
            
            if(!is_numeric($date)){
                $date = strtotime($date);
            }
            
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', $date));
            
        }
		
	public function view(){
                                
 	    header('Content-type: image/jpeg');
	    imagejpeg($this->imageResized, NULL, $this->quality);                          
	}
     
     
        /**
         * Сохранение файла
         *
         * @param string $file Путь к файлу (если не указан, записывает в исходный)
         * @param int $qualiti Качество сжатие JPEG
         */
        public function save($file, $rewrite=false)
        {
            if(empty($file)){
                throw new Exception('file path is empty');
            }
            
            if($rewrite!=true && file_exists($file)){
                    throw new Exception('File is already exists, turn on the rewrite mode');
            }
            
            if(!ImageJpeg($this->imageResized, $file, $this->quality)){
                throw new Exception('I cant save it');
            }
            
        }
    }
/* ------------------- // класс ---------------------------- */