<?php
/**
 * Class Tools
 */
class Tools extends CI_Controller
{
  public function __construct(){

    parent::__construct();

    if (!$this->input->is_cli_request()){
      exit('Direct access is not allowed. Terminal use only.');
    }

    $this->load->config('migration', TRUE);
    $this->load->dbforge();
  }

  public function help(){
    $result  = "Usage:\n\n";

    $commands = array(
      array(
        'command' => 'php index.php tools show',
        'desc'    => 'list migrations'
     ),
      array(
        'command' => 'php index.php tools migration <file_name>',
        'desc'    => 'create new migration file'
     ),
      array(
        'command' => 'php index.php tools migrate [version_number]',
        'desc'    => 'run [all] migrations'
     )
   );

    echo "Usage:\n\n";

    foreach ($commands as $command){
      echo sprintf("%-45s%35s\n", $command['command'], $command['desc']);
    }
    //echo $result . PHP_EOL;
  }

  //--------------------------------------------------------------------------

  public function show(){
    $this->load->helper('directory');

    $migrations = directory_map($this->config->item('migration_path', 'migration'));
    sort($migrations);
    foreach ($migrations as $migration){
      $info = explode('_', $migration);
      $version = $info[0];
      $name = explode('.', implode('_', array_slice($info, 1)))[0];

      echo $version . ' : ' . $name . PHP_EOL;
    }

  }

  //--------------------------------------------------------------------------

  public function migration($name){
    $this->make_migration_file($name);
  }

  //--------------------------------------------------------------------------

  public function migrate($version = null){
    $this->load->library('migration');

    if ($version != null){
      if ($this->migration->version($version) === FALSE){
        show_error($this->migration->error_string());
      } else {
        echo "Migrations completed successfully" . PHP_EOL;
      }

      return;
    }

    if ($this->migration->latest() === FALSE){
      show_error($this->migration->error_string());
    } else {
      echo "Migrations completed successfully" . PHP_EOL;
    }
  }

  //--------------------------------------------------------------------------

  protected function make_migration_file($name){
    $date = new DateTime();
    $timestamp = $date->format('YmdHis');

    $table_name = strtolower($name);

    $path = $this->config->item('migration_path', 'migration')
      . $timestamp . "_" . "{$name}.php";

    $my_migration = fopen($path, "w")
    or die("Unable to create migration file!");

    $migration_template = "<?php
class Migration_$name extends CI_Migration {

    public function up(){
        \$this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
           )
       ));
        \$this->dbforge->add_key('id', TRUE);
        \$this->dbforge->create_table('$table_name');
    }

    public function down(){
        \$this->dbforge->drop_table('$table_name');
    }
}";

    fwrite($my_migration, $migration_template);
    fclose($my_migration);
    echo "$path migration has successfully been created" . PHP_EOL;
  }

  //--------------------------------------------------------------------------
}