# Getting Started with Gauge

This is an example markdown specification file.
Every heading in this file is a scenario.
Every bulleted point is a step.

To execute this specification, use
	npm test

This is a context step that runs before every scenario
* Open items application

## CRUD items
* Add item id = "4" , item = "aaa"

* Must have id = "4" , item = "aaa"

* Update item id = "4" , item = "bbb"

* Must have id = "4" , item = "bbb"

* Delete item id = "4"

* Must not have id = "4"

