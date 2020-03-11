# Changelog

All notable changes to this project will be documented in this file. 

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).


- - - -


## v1.0.0 [2019-01-01]
- Initial Release


- - - -


## v1.0.0 [2019-01-14]
### Changed
- Empty pricing plans notification message for the user
- Introduce tool tips where needed
- Change PayPal logging config to 'ERROR'
- Show trial label on the user page to show the user current subscription status


### Fixed
- Remove 'admin_id' from refund migration and replace it with 'user_id'
- Show refund notification response
- Bug with PayPal charging initial payment for trial plans