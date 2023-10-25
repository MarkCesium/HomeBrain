"# HomeBrain"

```
request:
GET http://homebrain2/api/sensors/get
Authorization: Bearer <token, len=480>
Content-Type: application/json

response:
GET http://homebrain2/api/sensors/get

{
    "data": [
        {
            "id": 1,
            "responseType": "int",
            "publisherDescriptions": {
            "pin_input": "3",
            "handler": "getDataBool",
            "srd": "500",
            "reaction": "digitalSignal",
            "device_pin_out": "4"
        }
        },
        {
            "id": 2,
            "responseType": "int",
            "publisherDescriptions": []
        },
        {
            "id": 3,
            "responseType": "int",
            "publisherDescriptions": []
        },
        {
            "id": 4,
            "responseType": "int",
            "publisherDescriptions": []
        },
        {
            "id": 10,
            "responseType": "bool",
            "publisherDescriptions": []
        },
        {
            "id": 14,
            "responseType": "int",
            "publisherDescriptions": {
            "pin_input": "2"
        }
        },
        {
            "id": 16,
            "responseType": "float",
            "publisherDescriptions": {
         "pin_out": "6"
        }
        },
        {
            "id": 17,
            "responseType": "int",
            "publisherDescriptions": {
            "pin_out": "8"
        }
        }
    ],
    "status": 1
}
```

```
request:
POST http://homebrain2/api/api-authentication
Content-Type: application/json

{
  "username": "e001",
  "password": "1234"
}

response:
POST http://homebrain2/api/api-authentication

{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2OTY2MDUwMjAsImV4cCI6MTY5NjYwODYyMCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiZTAwMSJ9.L3F7aHNZ481IW_XEwJzmuoHuAxhZILmAmfkjpQvIcfvfNdr8vV46cKSXphT0jePv7rTx3s96h1QmP6_LE8TvNkpcMfs8v-01tYo7ghaPkS4zOrqgOS7iiUuL3ZRasMaYItM0B7IA5dR-OH-QRoV9vMC4tXupVxPM-K8wuVGfrY-uba50SRb_Nspd0E8P8BOiR-UBDXyUNdLHbYbuqmle1OWJ0-T7oJ2LgRIhTaZnHBXetHx9dpvH-aWXRbMOe0Kry2irnbphGmTsboTWn5YJcN0YPDBSYLJLfnHOj4JIqZf0rcdWi90gyOS9lD2CEyw1lnfLnVg-Gy_nWRcoMhYjSA"
}
```

```
request:
GET http://homebrain2/api/api-hello
Authorization: Bearer <token, len=480>
Content-Type: application/json

response:
GET http://homebrain2/api/api-hello

{
  "msg": "Hello, World!"
}
```
