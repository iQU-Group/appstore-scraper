# appstore-scraper
Scrapes the app store for apps

## Usage
Run `composer install` in order to download the dependencies.

Every app in App Store has a unique integer id that we call it softwareId in all the classes. 
Below, you can find a description of the 2 most important classes and their methods:

 - SoftwareInfo.php: Fetches information for a particular softwareId.
	 - getAllInfo fetches all the information for a given softwareId from the store that you specify in countryCode.
	 - getFilteredInfo fetches the response, parses the fields and returns the filtered results.
 - SoftwareReviews.php: Fetches reviews for a particular softwareId.
	 - getOnePageReviews fetches the reviews for only one page. App Store allows us to fetch 10 pages of reviews for an app, each page containing 50 reviews.
	 - getAllPagesReviews fetches all the reviews for a particular softwareId by fetching a total of 10 pages of reviews. Returns an array of reviews.
 
## Directory structure
- config/: Should contain all the config files.
- examples/: Contains example responses from AppStore.
- src/: Contains the classes that can be used in order to fetch information and reviews for software.

