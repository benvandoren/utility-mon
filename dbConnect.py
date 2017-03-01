# ReName this file to dbConnect.py fill in hostname (eg. localhost or 127.0.0.1) and password
dbConn  = pymysql.connect(user='utility_mon', password='password', host='hostname', database='UtilityMon', autocommit=True)
