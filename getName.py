import MySQLdb
db = MySQLdb.connect(host="localhost",
    user="root", passwd="root", db="jgate_modbus")
cursor = db.cursor()
cursor.execute("SELECT * FROM device_info;")
results = cursor.fetchall()
print(results[0][4])