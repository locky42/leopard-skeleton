# Маршрутизація через YAML

## 1) Явні маршрути (`routes`)

```yaml
routes:
  - controller: Site/UserController
    action: getProfile
    method: GET
    path: /profile/{id}

  - controller: "\\App\\Controllers\\Admin\\DashboardController"
    action: index
    method: GET
    path: /admin
```

- `controller`:
  - відносне значення резолвиться як `App\\Controllers\\{controller}`
  - можна передати абсолютний FQCN (з `\\` на початку)
- `action`: ім'я публічного методу контролера
- `method`: HTTP-метод (зберігається у верхньому регістрі)
- `path`: шлях маршруту з опціональними динамічними параметрами

## 2) Групи контролерів (`controllers`)

```yaml
controllers:
  - controller: Tools/HashController
    path: /tools

  - namespace: Api
    path: /api
```

Підтримуються 2 типи записів:

- `controller` + `path`: реєстрація одного контролера для auto-routing
- `namespace` + `path`: сканування всіх `*Controller.php` у `src/Controllers/{namespace}`

Для auto-routing обробляються тільки методи, що закінчуються на `Action`.

## 3) Конвенції auto-routing

Для методів виду `*Action`:

- HTTP-префікс визначається з назви методу: `get|post|put|delete|patch|options|head`
- якщо префікса немає, використовується `GET`
- `indexAction` мапиться на корінь контролера
- camelCase/snake_case в назві action конвертуються у kebab-case в URL

Приклади:

- `getProfileAction` → `GET .../profile`
- `postSaveDataAction` → `POST .../save-data`
- `aboutAction` → `GET .../about`

## 4) Правила для `controllers[].path`

- `path: /` → `/{controller}` і `/{controller}/{action}`
- `path: ""` → `/` і `/{action}`
- `path: /base` → `/base/{controller}` і `/base/{controller}/{action}`

## 5) Динамічні параметри

Роутер підтримує:

- `{id}`: один сегмент URI
- `{id:\\d+}`: кастомне regex-обмеження
- `{path}`: жадібний параметр (може містити `/`)

Якщо тип параметра в методі контролера `int|float|bool` і конвертація неуспішна, роутер повертає `404`.

`HEAD`-запити можуть матчитися на `GET`-маршрути.
