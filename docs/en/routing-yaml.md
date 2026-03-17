
# Routing via YAML

## 1) Explicit routes (`routes`)

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
  - relative value is resolved as `App\\Controllers\\{controller}`
  - absolute FQCN can be used (leading `\\`)
- `action`: public controller method name
- `method`: HTTP method (stored uppercased)
- `path`: route path with optional dynamic params

## 2) Controller groups (`controllers`)

```yaml
controllers:
  - controller: Tools/HashController
    path: /tools

  - namespace: Api
    path: /api
```

Supported entries:

- `controller` + `path`: register one controller for auto-routing
- `namespace` + `path`: scan all `*Controller.php` in `src/Controllers/{namespace}` and register them

Only methods ending with `Action` are auto-routed.

## 3) Auto-routing conventions

For controller methods ending with `Action`:

- HTTP prefix is detected from method name: `get|post|put|delete|patch|options|head`
- if no prefix exists, method defaults to `GET`
- `indexAction` maps to controller root path
- camelCase/snake_case action names are converted to kebab-case path segments

Examples:

- `getProfileAction` → `GET .../profile`
- `postSaveDataAction` → `POST .../save-data`
- `aboutAction` → `GET .../about`

## 4) Base path rules (`controllers[].path`)

- `path: /` → `/{controller}` and `/{controller}/{action}`
- `path: ""` → `/` and `/{action}`
- `path: /base` → `/base/{controller}` and `/base/{controller}/{action}`

## 5) Dynamic parameters

Router supports:

- `{id}`: one URI segment
- `{id:\\d+}`: custom regex
- `{path}`: greedy parameter (captures `/`)

If controller parameter type is `int|float|bool` and conversion fails, router returns `404`.

`HEAD` requests can match `GET` routes.
