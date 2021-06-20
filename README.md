# My Dashboard Is King

Sometimes you want to enforce a certain dashboard layout to other users.

This extension allows to set the dashboard of one or more contacts to the same layout as a (master) contact.

The master contact that serves as template for the other contacts is called the king :-)

## Setup

The API v3 to execute is **Dashboard.Setfromking**

Parameters:
 * **king_id** (required) - the contact id of the contact with the dashboard layout you want to enforce to other users
 * **subject_ids** (optional) - comma separated list of contact id's. If empty all contacts will be affected

## Typical use

You define a good dashboard layout for yourself, then you use your contact id as "king" in the API.

Depending if it's a one shot or a permanent enforcement, you would excecute the API call once or define a scheduled job.
