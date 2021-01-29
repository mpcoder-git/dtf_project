<?php
class Route
{
    static function start()
    {
        // контроллер и действие по умолчанию
        $controller_name = 'Main';
        $action_name = 'index';
        
        $routes = explode('/', $_SERVER['REQUEST_URI']);
        
		// получаем имя контроллера
        if ( !empty($routes[1]) )
        { 
            $controller_name = $routes[1];
        }
        
        // получаем имя экшена
        if ( !empty($routes[2]) )
        {
            $action_name = $routes[2];
        }

		//если нет GET массива, параметры считываем из адреса. Иначе переменные считываем из массива GET
		if((!$_GET) && ($controller_name != 'deleteattachfile')){
			// получаем параметр экшена
			if ( !empty($routes[3]) )
			{
				$action_parameter = $routes[3];
			}
			
			// получаем параметр экшена
			if ( !empty($routes[4]) )
			{
				$action_parameter2 = $routes[4];
			}
		} else {
			//перебор массива GET и присваивание переменных
			
			$count = 0; //текущий элемент массива иил общее количество строк массива
			foreach ($_GET as $k=>$v) { 
				$count = $count + 1;
				if ($count == 1){ $action_parameter = $v; }
				if ($count == 2){ $action_parameter2 = $v; }
				//echo "Значение переменной $k - $v </br>";
			}
			//обрезать название экшена после знака вопроса
			$action_name = substr($action_name, 0, strpos($action_name, "?"));
		}
		
		
		
        // добавляем префиксы
        $model_name = 'Model_'.$controller_name;
        $controller_name = 'Controller_'.$controller_name;
        $action_name = 'action_'.$action_name;

        // подцепляем файл с классом модели (файла модели может и не быть)

        $model_file = strtolower($model_name).'.php';
        $model_path = "./Models/".$model_file;
        if(file_exists($model_path))
        {
            include "./Models/".$model_file;
        }

        // подцепляем файл с классом контроллера
        $controller_file = strtolower($controller_name).'.php';
        $controller_path = "./Controllers/".$controller_file;
        

		
		if(file_exists($controller_path))
        {
            include "./Controllers/".$controller_file;
        }
        else
        {
           /*
            правильно было бы кинуть здесь исключение,
            но для упрощения сразу сделаем редирект на страницу 404
            */
            Route::ErrorPage404();
        }
        
        // создаем контроллер
        $controller = new $controller_name;
        $action = $action_name;
		
        if(method_exists($controller, $action))
        {

			// вызываем действие контроллера
            if ( empty($action_parameter) ){
				$controller->$action();
				
			} else {

				//запуск экшена с параметром, если он есть				
				if (empty($action_parameter2)){
					//print $action_parameter.' p1 '.$action_parameter2.' '.$action; exit;
					$controller->$action($action_parameter);
				} else {
					//print $action_parameter.' p2 '.$action_parameter2.' '.$action; exit;
					$controller->$action($action_parameter,$action_parameter2);
				}
				 
			}			
				
        }
        else
        {
            // здесь также разумнее было бы кинуть исключение
            Route::ErrorPage404();
        }
    
    }
    
    function ErrorPage404()
    {
        $host = 'http://'.$_SERVER['HTTP_HOST'].'/';
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}
?>