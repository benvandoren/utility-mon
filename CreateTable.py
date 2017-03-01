#!/usr/bin/env python3.4
# Calculate DB Size:
# select table_schema "UtilityMeter", sum(data_length + index_length) / 1024 / 1024 "DB size in MB", sum(data_free)/ 1024 / 1024 "Free Space in MB" from information_schema.tables group by table_schema;
import pymysql

# First create a user 'utility_mon' and DB UtilityMon
# create table UtilityMeter (mPrimaryKey int(6) unsigned auto_increment primary key, mId int(11), mType int(2), mTime int(11), mTotalConsumption int(11), mConsumed float(11));
exec(open("dbConnect.py").read()) # dbConn  = pymysql.connect(user='utility_mon', password='password', host='hostname', database='UtilityMon', autocommit=True)
dbCur   = dbConn.cursor()
dbCur.execute("create table UtilityMeter (mPrimaryKey int(6) unsigned auto_increment primary key, mId varchar(50), mType int(2), mTime int(11), mTotalConsumption int(11), mConsumed float(11));")
dbCur.close()
dbConn.close()
