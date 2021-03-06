<?php

        /*
        *       Description:    数据库基础类(可在此类,或子类(model类)中增加错误状态码,针对错误做合理处理,使错误可控)
        *
        *
        */
        class db_class{
                       
                        private $db_server;                     //数据库服务器地址
                        private $db_name;                       //数据库名
                        private $db_username;           //数据库用户名
                        private $db_userpass;           //数据库用户密码
                        private $dblink = '';           //数据库连接
                       
                        /*      ()
                        *       @Description:   初始化数据库->服务器地址,数据库名,用户名,密码       连接数据库

                                @param  $server         数据库服务器地址        |
                                                $db_name        数据库名                    |
                                                $user           数据库用户名          |
                                                $pass           数据库用户密码         |
                                                $charset        数据库默认编码         |
                        *
                        *
                        */
                        public function __construct($db_server='localhost', $db_name='yanghongxia', $db_username='yanghongxia', $db_userpass='zhushengwen', $charset='utf8'){

                                $this -> db_name = $db_name;
                                $this -> db_server = $db_server;
                                $this -> db_username = $db_username;
                                $this -> db_userpass = $db_userpass;
                          
                                //连接数据库
                                $this->db_conn( $db_server, $db_name, $db_username, $db_userpass, $charset );
                        }
                        function __destruct() {
                               $this-> db_close();
                           }
                       
                        /*      ()
                        *       @Description:   连接,使用数据库,设置默认编码

                                @param  $server         数据库服务器地址        |
                                                $db_name        数据库名                    |
                                                $user           数据库用户名          |
                                                $pass           数据库用户密码         |
                                                $charset        数据库默认编码         |
                        *
                        *
                        */
                        private function db_conn( $db_server, $db_name, $db_username, $db_userpass, $charset ){
                               
                                //打开一个到 MySQL 服务器的连接
                                $this -> dblink = mysql_connect( $db_server, $db_username, $db_userpass );
                                if(!$this->dblink){
                                        $this -> dblink = '';
                                        die('Error:不能连接到Mysql: '.mysql_error());
                                }
                                //使用数据库
                                if( !(mysql_query( 'USE '.$db_name )) ){
                                        die("Error:不能正确使用数据库,错误信息：".mysql_error());
                                }
                                //设置编码
                                if( !(mysql_query( 'SET NAMES "'.$charset.'"' )) ){
                                        die("Error:不能正确设置 ".$charset." 编码".mysql_error() );
                                }
                        }
                       
                        /*      ()
                        *       @Description:   关闭数据库连接

                                @param  none
                        *
                        *
                        */
                        public function db_close(){
                               if(!$this->dblink) mysql_close( $this->dblink );
                        }
                       
                        /*()
                        *       Description: 插入记录

                        *       @Param  $table          操作表
                        *       @Param  $coloumns       字段              |       空
                        *       @Param  $values         值
                        */
                       

                        public function db_insert( $table, $coloumns = '', $values ){
                               
                                if( $coloumns ){

                                        $sql = 'INSERT INTO '.$table.' ('.$coloumns.') VALUES ('.$values.')';

                                }else{

                                        $sql = 'INSERT INTO '.$table.' VALUES ('.$values.')';

                                }

                                return mysql_query( $sql, $this -> dblink );
                        }

                       
                       
                        /*()
                        *       Description: 删除记录

                        *       @Param          $table                  操作表
                        *       @Param          $conditions             条件
                        */
                        public function db_delete( $table, $conditions = '' ){
                               
                                if( $conditions != '' ){

                                        $sql = 'DELETE FROM '.$table.' WHERE '.$conditions;

                                }else{

                                        $sql =  'DELETE FROM '.$table;

                                }
                               
                                if( !mysql_query( $sql, $this->dblink ) ){

                                        die( 'Delete Error :'.mysql_error() );

                                }
                        }
                       
                        /*()
                        *       Description: 更新记录
                        *       @Param          $table                  操作表
                        *       @Param          $modify                 修改的列值
                        *       @Param          $conditions             依据的条件列值
                        */
                        public function db_update( $table, $modify, $conditions ){

                                $sql = 'UPDATE '.$table.' SET '.$modify.' WHERE '.$conditions;
                               
                               return mysql_query( $sql,$this->dblink ) ;
                        }
                       
                        /*      ()
                        *       @Description:   查询记录

                                @param  $table                  操作的表
                                                $columns                操作的列
                                                $conditions             查询条件

                                @return         $result         符合条件的结果集二维数组
                        *
                        *
                        */
                        public function db_select( $table, $columns = '*', $conditions = ' 1=1 ' ){
                               
                                //为以后测试需要,暂时保留测试代码
                                /*
                                echo $table;
                                echo "<br/>";
                                echo $columns;
                                echo "<br/>";
                                echo $conditions;
                                echo "<br/>";
                                */
                                        $sql = 'SELECT '.$columns.' FROM '.$table.' WHERE '.$conditions;

                                /*
                                echo $sql;
                                echo "<br/>";
                                */

                                //为以后测试需要,暂时保留测试代码
                                //echo $sql;

                                if( !($handle = mysql_query( $sql, $this->dblink )) ){

                                        die( "SELECT Error :".mysql_error() );

                                }

                                while( $result[] = mysql_fetch_array( $handle, MYSQL_ASSOC ) ){};
                               
                                //弹出返回信息数组的最后一个空单元
                                array_pop( $result );

                                return $result;
                        }
                }
?>