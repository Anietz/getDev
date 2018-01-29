---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.
[Get Postman Collection](http://localhost/docs/collection.json)
<!-- END_INFO -->

#general
<!-- START_8c0e48cd8efa861b308fc45872ff0837 -->
## Authenticate a user

This endpoint is used to authenticate a registered user. This request is not authenticated.

> Example request:

```bash
curl -X POST "http://localhost/api/v1/login" \
-H "Accept: application/json" \
    -d "email"="dolorum" \
    -d "password"="dolorum" \

```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/login",
    "method": "POST",
    "data": {
        "email": "dolorum",
        "password": "dolorum"
},
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/login`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | 
    password | string |  required  | 

<!-- END_8c0e48cd8efa861b308fc45872ff0837 -->

<!-- START_8ae5d428da27b2b014dc767c2f19a813 -->
## Register a user

This endpoint is used to register a user. This request is not authenticated.

> Example request:

```bash
curl -X POST "http://localhost/api/v1/register" \
-H "Accept: application/json" \
    -d "name"="dolor" \
    -d "email"="mwilkinson@example.com" \
    -d "password"="dolor" \

```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/register",
    "method": "POST",
    "data": {
        "name": "dolor",
        "email": "mwilkinson@example.com",
        "password": "dolor"
},
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/register`

#### Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    name | string |  required  | 
    email | email |  required  | 
    password | string |  required  | 

<!-- END_8ae5d428da27b2b014dc767c2f19a813 -->

<!-- START_ca4f61aae14da00cb88769e6337cade9 -->
## Get facebook auth token

This endpoint is used to get the token from facebook auth endpoint.

> Example request:

```bash
curl -X POST "http://localhost/api/v1/getFacebookAuth" \
-H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/getFacebookAuth",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/getFacebookAuth`


<!-- END_ca4f61aae14da00cb88769e6337cade9 -->

<!-- START_c0aef738a5279c536230b83be8fb027d -->
## Get practice question

This endpoint is used to get questions. This request is authenticated.

> Example request:

```bash
curl -X GET "http://localhost/api/v1/get_questions" \
-H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/get_questions",
    "method": "GET",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```

> Example response:

```json
{
    "message": "Unauthenticated."
}
```

### HTTP Request
`GET api/v1/get_questions`

`HEAD api/v1/get_questions`


<!-- END_c0aef738a5279c536230b83be8fb027d -->

<!-- START_96b8840d06e94c53a87e83e9edfb44eb -->
## Get an authenticated user

This endpoint is used to get a current logged in user. This request is authenticated.

> Example request:

```bash
curl -X POST "http://localhost/api/v1/user" \
-H "Accept: application/json"
```

```javascript
var settings = {
    "async": true,
    "crossDomain": true,
    "url": "http://localhost/api/v1/user",
    "method": "POST",
    "headers": {
        "accept": "application/json"
    }
}

$.ajax(settings).done(function (response) {
    console.log(response);
});
```


### HTTP Request
`POST api/v1/user`


<!-- END_96b8840d06e94c53a87e83e9edfb44eb -->

