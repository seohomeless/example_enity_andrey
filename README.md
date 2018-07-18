## Test work for enity Drupal 8

При установке модуль создает записи только в базе example_field_data рандомно. Не полуилось связать базы с полямя сущности, нужно создавать несколько таблиц.
Не выводит в админке, но сайте показываеть.
Сделал модуль, через Drupal Console так как долго не получалось с нуля. В контроллере добавил поля title, description, url, вывел их при создание в
админке и отображение.


### Функция для выборки поле с базы
```
/**
 * Implements hook_page for my array in the enity.
 */
function bartik_preprocess_page(&$var) {
		$query = \Drupal::database()->select('example_field_data', 'nfd');
		$query->fields('nfd', ['title', 'url', 'description']);
		$result = $query->execute()->fetchAll();

		$var['results'] = $result;
}
```

### Вывод в твиг шаблоне
```
<table>
{% for result in results %}
<tr><td><h2>{{ result.title }}</h2>{{ result.description }}<br>Ссылка: {{ result.url }}</td></tr>
{% endfor %}
</table>	
```
