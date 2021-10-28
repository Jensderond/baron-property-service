# Baron property service

This is a symfony project that retrieves properties from an external resource like [Pyber](https://www.pyber.nl/).

### To do:
 - [x] Create a command for importing properties instead of an endpoint.
 - [x] Add all useful property information
 - [x] Add filter endpoints
   - [x] Property type ( house / apartment )
   - [x] Sold properties ( based on archived ? )
   - [x] For sale
   - [x] For rental
   - [x] For sale and rental
   - [x] By location ?
   - [x] By price

## Getting started
This project uses [DDEV](https://ddev.readthedocs.io/en/stable/) as a development environment.

1. Run the project by using `ddev start`
2. Install dependencies using `ddev composer install`
3. Check the [API documentation](https://baron-property-service.nl.local/api/properties)