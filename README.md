# Baron property service

This is a symfony project that retrieves properties from an external resource like [Pyber](https://www.pyber.nl/).

### To do:
 - [ ] Create a command for importing properties instead of an endpoint.
 - [ ] Add all useful property information
 - [ ] Add filter endpoints
   - [ ] Property type ( house / apartment )
   - [ ] Sold properties ( based on archived ? )
   - [ ] For sale
   - [ ] For rental
   - [ ] For sale and rental
   - [ ] By location ?
   - [ ] By price

## Getting started
This project uses [DDEV](https://ddev.readthedocs.io/en/stable/) as a development environment.

1. Run the project by using `ddev start`
2. Install dependencies using `ddev composer install`
