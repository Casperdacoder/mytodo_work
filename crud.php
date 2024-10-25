<?php

 abstract class Crud {

  abstract public function show($id);

  abstract public function store($param1, $param2);

  abstract public function update($param1, $param2);

  abstract public function delete($id);

}