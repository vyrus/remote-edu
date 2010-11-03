<h2>Слушатель <?php printf('%s %s %s', $this->user_info['surname'], $this->user_info['name'], $this->user_info['patronymic']); ?></h2>
<h3>Отдельные дисциплины, изучаемые слушателем</h3>
<pre><?php print_r($this->disciplines); ?></pre>
<h3>Дисциплины, входящие в программы, изучаемые слушателем</h3>
<pre><?php print_r($this->disciplines_programs); ?></pre>

<pre><?php //print_r($this->checkpoints); ?></pre>