# About

This project exposes a command that accepts a Github user as a parameter. It will return how many followers is the user following back.

Makes use of the [Github REST API](https://docs.Github.com/en/rest).

## Set up

In order to set up the app in a Docker environment you have to:

1) Copy `.env.dist` to `.env`
2) Copy `code/app/.env.dist` to `code/app/.env`
3) Run:
    ```
    docker-compose up
    ```

## Running the command

Once Docker is up, you can execute the command to get user followbacks.

Some examples:
```
docker-compose exec php php bin/console github:followbacks -u ramsey
docker-compose exec php php bin/console github:followbacks -u ocramius
docker-compose exec php php bin/console github:followbacks -u sdras
```

The output will be something like:
```
{
    "userName": "ramsey",
    "followBacks": {
        "count": 42,
        "userNames": ["vlucas", "funkatron", "tswicegood", "pkeane", "elazar", "nateabele", "dotjay", "BigBlueHat", "dshafik", "enygma", "ralphschindler", "grahamc", "jlleblanc", "bradley-holt", "rdohms", "ashnazg", "wez", "harikt", "weaverryan", "awebneck", "convissor", "mfacenet", "maggie1000", "mospired", "jeremykendall", "saklay", "chrisspruck", "nicksloan", "marijn", "jwoodcock", "joshuamorse", "dmora", "epochblue", "codewaters", "kitglen", "jennharbin22", "jsundquist", "fabre-thibaud", "matthewtrask", "syntacticNaCl", "colinodell", "mwhitney"]
    }
}
```

## Github API rate limit

Github REST API has a rate limit policy. It will let you make 50 requests/hour if you are not authenticated. That could be a problem if you want to get followbacks for Github users with thousands followers/following.

The number of request per hour can be increased with a personal access token. [Create one](https://docs.github.com/en/github/authenticating-to-github/creating-a-personal-access-token#creating-a-token) (for this case we only need the `read:user` permission) and copy/paste it on the env variable `GITHUB_API_TOKEN` located in `code/app/.env`

## Warning

Bear in mind that using this tool for Github users with thousands followers/following would take some time. This POC has been developed with a sequential API call approach for simplicity.

## Possible improvements

* Make parallel requests to the API to gain speed in getting results
