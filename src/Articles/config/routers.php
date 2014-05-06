<?php

return [
    ["/articles", ["index", "list"]],
    ["/articles/id", ["index", "view"]],
    ["/api/article/dates", ["api", "dates"]],

    ["/admin/articles", ["admin", "list"]],
    ["/admin/articles/add", ["admin", "add"]],
    ["/admin/articles/edit", ["admin", "edit"]],
    ["/admin/articles/save", ["admin", "save"]],
    ["/admin/articles/rm", ["admin", "rm"]],
    ["/admin/articles/category", ["admin", "categoryList"]],

];