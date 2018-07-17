Redbrain Test

Routes

GET /api/commit


Query Params
user    string  required
repo    string  required
from    date    required (YYYY-MM-DD)
to      date    required (YYYY-MM-DD)
author-username required


Example

api/commit?user=laravel&repo=laravel&from=2018-07-01&to=2018-07-16&author-username=taylorotwell

Response:

{
    "user": "laravel",
    "repo": "laravel",
    "from": "2018-07-01",
    "to": "2018-07-16",
    "author-username": "taylorotwell",
    "results": {
        "total_additions": 18,
        "total_deletions": 1,
        "commits": [
            {
                "sha": "fa81e36841ee25c3440fc430ed8d6b66c641062b",
                "additions": 18,
                "deletions": 1
            }
        ]
    }
}

Your github credentials must be stored in .env

GITHUB_USERNAME=
GITHUB_PASSWORD=

e.g.
GITHUB_USERNAME=mrsmith
GITHUB_PASSWORD=mysecret


The route for the app can be found at:
routes/api.php

The controller for the app can be found at:
app/Http/Controllers/ProjectController.php
