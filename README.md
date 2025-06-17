# Introduction
This repository in intended to process a file with debtors unknown structure data to an internal structure data that we can use.

# Installation
Clone the project: 

`git clone https://github.com/RodrigoBattagliero/debtor-importer.git`

Run `cd debtor-importer`

Modify `src/.env`

`cp src/.env.example src/.env`

make sure to have the correct data in this keys

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=wayni
DB_USERNAME=wayni
DB_PASSWORD=wayni
```

Run docker compose
`docker compose up -d`

Enter container shell

`docker compose exec php sh`

In this shell run as follow:
- Install dependencies `composer install`
- Run migrations `php artisan migrate`
- Start workers (you can use two terminals)
    - `php artisan queue:work --queue=update`
    - `php artisan queue:work --queue=default`


# Usage

### /deudores/upload

Receives a file and email address, process the file and send an email to the provided address.
Process the file extracting from every line: Cuit, amount, entity code and maximun situation. 
This data is used to fill internal structure according to the specifications: debtor (cuit, amount, max_situation), institution (code, amount). 
Under the hood, the file data is queue for async process.
When the process is finished, an email is sent to the address passed along with the file.

**Important** the file must be .csv or .txt and it should be smaller than 2MB.

**Request Body**
```json
{
    "file": "file.csv",
    "email": "rbattalgiero@gmail.com"
}
```

**Response** 

`Code 204 No Content`

### POST /deudores/procesar-archivo
This endpoint is intended to be used for big file proccesing. 
Perform the same functionality than upload, but intead of passing a file, this just expect the filename (which should be store in some kind of thirsd service or directly on the project's server, depending of the project configuration).

**Request Body**
```json
{
    "filename": "file.csv",
    "email": "rbattalgiero@gmail.com"
}
```

**Response** 
`Code 204 No Content`

### /deudores/top/{n?}

Returns a list of debtor ordered by amount. Opcionally, you can indicate the size of the list to be returned.

**Request**

`GET /deudores/top/10`

**Response**
```json
[
	{
		"id": 115,
		"cuit": "11111111111",
		"max_situation": 5,
		"amount": 0,
		"created_at": "2025-06-14T21:59:08.000000Z",
		"updated_at": "2025-06-15T03:47:29.000000Z"
	},
    
    ...
]
```


### /deudores/{cuit}
Find a debtor by cuit and returns its data.

**Request**

`GET /deudores/11111111111`

**Response**
```json
{
	"id": 115,
	"cuit": "11111111111",
	"max_situation": 5,
	"amount": 0,
	"created_at": "2025-06-14T21:59:08.000000Z",
	"updated_at": "2025-06-15T03:47:29.000000Z"
}
```

### /entidades/{code}
Find a entity by code and returns its data.

**Request**

`GET /entidades/7`

**Response**
```json
{
	"id": 1,
	"code": "7",
	"amount": 11120450,
	"created_at": "2025-06-14T21:59:07.000000Z",
	"updated_at": "2025-06-16T00:23:28.000000Z"
}

```

## Testing

**In order to execute testing class, make sure to configure testing database.**

Run `cp .env env.testing`

> Change `DB_DATABASE` you can add '_test'. 

Enter container shell

`docker compose exec php sh`

Run migrations `php artisan migrate --env=testing`

Run `php artisan test`