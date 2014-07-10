#!/bin/bash

mysql -t -p < <(cat drop.sql;
    cat database.sql;
    cat data.sql;
    cat showdata.sql)
