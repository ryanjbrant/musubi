# Changelog

All notable changes to this project will be documented in this file.

## [1.0.0] - 2015-12-03

## Added

	2017-04-07 - Email Features Added for user

	2017-12-25 - Admin - Email verification function

### Removed - 2017-11-30 

	- Embedded link feature partially removed

### Changed 

	2017-11-08 - 3d6e1d2 - Segregated settings options.

### Fixed

	- Twitter login issue fixed.

	- 66da49d Social login - cancel button error fixed

	- API Youtube link issue fixed - Refer Commit ID -  4697c37

	## 2017-12-11

	- Changed Error message "Admin disbled the account" as "Please verify your email", While login the unverified user.

	- In payperview option, if the user paid the video, eventhough if he entered the pay perview pay page url directly , he can again pay the amount. The issue fixed and redirect into the particular paid video.

	- Demo user can loggin in "n" of browser windows. But other registered user can loggin their account based on their sub accounts count.

	- Social Icons like Pinterest, Linkedin, facebook, twitter and so on displayed in footer.

	- App & Playstore icons placed in footer page.

	- Forgot Password Email - While clicking "visit our website" , it will redirect into admin panel. It was fixed and redirect into user page.

	- Changed "Join free for month" into "Join Free for a month"

	- Fixed the issue - The video doesn't play on desktops. Flash is enabled both on Safari and Chrome - tested in both the browsers. Video is not playing in both desktop browser. Same video plays in Android app

	- Admin Panel - Based on sub profiles displayed wishlist & History.

	- In categories page mylist icon is not working - Fixed the issue

	- After search cleared the input field

	- If user logged in through social login, in edit account hided his email id

	- In Edit profile, done mobile number validation

	- Billing Details - Integrated with backend code and displayed.

	- Fixed Issue in Get wishlists based on logged in user sub account

	- Give frontend url view page in page list.

	- Added seeder for basic pages.

	- Add Page -> If page already created - remove from select box except others

	- Admin can create "n" of "other" pages.

	- Revenue settings are not updating issue fixed.

	- Changed heading "user commission" to "Moderator commission"

	- Body Scripts is not updating and its not affecting in angular as well - The issue fixed

	- Subscription create/edit - Allow amount in float value

	- Subscription Payment missing from menu bar - Fixed

	- Displayed banner image while admin edit

	- Hided Languages - Because in front end , we don't have multilanguage option

	- Moved "Spam Videos" into Videos section

	- Videos -> Give link to view the single video page 

	- Videos -> PPV -> Without selecting user type and subscription type also PPV applying for user - Fixed by selected default value for both fields


	## 12.12.2017

	- Banner Video - Frontend Image validation Done.

	- Video Upload - Frontend Image validation done.

	- Edit video - Removed unwanted subtitle field.

	- Moderator - View video - Not displayed video. - Issue fixed

	- Pay per view videos list displayed based on logged in user. If he paid any of the videos

	- Mobile  - Banner videos -> Spam videos also displaying. The issue is fixed by restricted spam videos from the banner videos


	## 13.12.2017

	- Youtube Videos link not played. - Issue fixed

	- Trailer video not played -Issue fixed

	- In Mobile view - Site logo will change into site icon

	- Payment History - Paid video details displayed. 

	## 25/12/2017

	- Fixed moderator commission spilit issue in admin and user api.
