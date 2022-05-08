object = {
    "id": 1,
    "name": "mohamed",
    "email": "mohamed99elsokary@gmail.com",
    "mobile": "+201111155856",
}


def new_resource(data, required):
    new_object = {}
    for i in required:
        new_object[i] = data[i]
    print(new_object)


new_resource(object, ["id", "name", "email"])
