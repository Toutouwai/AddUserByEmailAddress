# Add User By Email Address

Requests an email address instead of a user name in the first step of Add User. 

This is useful if a site is configured for users to log in with their email address rather than a user name, in which case the name of the user account is less relevant from the user's point of view. If non-superusers are allowed to add users they may not understand why they are being asked to enter a user name or may be irritated at having to invent a name for the user.

The module changes this...

![aubea-1](https://github.com/Toutouwai/AddUserByEmailAddress/assets/1538852/bfe5ce4c-fac4-4ed4-a4c3-f29d5c1ef98a)

...to this...

![aubea-2](https://github.com/Toutouwai/AddUserByEmailAddress/assets/1538852/50307414-9c20-443e-90a1-1969f8ca031c)

## Requirements

The module only has an effect if you have done the following:

1. Ticked the "Unique" checkbox on the "Advanced" tab of the "email" field settings.
2. Selected "Email address" as the "Login type" in the settings of the ProcessLogin module.

## Configuration options

When a user is added a name for the user is generated automatically. In the module config you can choose whether this automatic name should be derived from the user's email address...

* "Always" - the user name is kept in sync with the email address if the email address later changes.
* "Only when a user is first added" - the user name will not be automatically changed if the email address changes.
* "Never" - the user name is a random string and isn't derived from the email address.

![aubea-4](https://github.com/Toutouwai/AddUserByEmailAddress/assets/1538852/7ab7b869-28a6-45f3-a42f-00bea62ccd4d)

Advanced: if you want to customise the way that the user name is derived from the email address you can hook after `AddUserByEmailAddress::deriveNameFromEmail()`.

## Tips

If your site is configured for users to log in with their email address you'll probably want to set "Masthead + navigation > User navigation label format" to `{email}` in the AdminThemeUikit module settings.

![aubea-3](https://github.com/Toutouwai/AddUserByEmailAddress/assets/1538852/037b1fb7-bc7b-4bb7-afc1-1ad7b5c594c5)
