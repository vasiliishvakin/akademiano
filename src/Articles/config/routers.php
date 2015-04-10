<?php

return [
    ["/articles", ["index", "list"]],
    ["/articles/id", ["index", "view"]],
    ["/api/article/dates", ["api", "dates"]],

    ["/admin/articles", ["admin", "list"]],
    ["/admin/articles/add", ["admin", "form"]],
    ["/admin/articles/edit", ["admin", "form"]],
    ["/admin/articles/save", ["admin", "save"]],
    ["/admin/articles/rm", ["admin", "rm"]],
    ["/admin/articles/category", ["admin", "categoryList"]],

];