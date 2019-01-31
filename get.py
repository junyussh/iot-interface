import sys
from uuid import getnode as get_mac
import MySQLdb

database_config = {
    "host": "localhost",
    "name": "jgate_modbus",
    "username": "root",
    "password": "root"
}

def main():
    arg = sys.argv[1]
    if(arg == "MAC"):
        mac = get_mac()
        mac = ':'.join(("%012X" % mac)[i:i+2] for i in range(0, 12, 2))
        print(mac)
    elif (arg == "Name"):
        db = MySQLdb.connect(host=database_config["host"],
        user=database_config["username"], passwd=database_config["password"], db=database_config["name"])
        cursor = db.cursor()
        cursor.execute("select * from system where name='system_name';")
        results = cursor.fetchone()
        print(results[3])
    elif (arg == "domain"):
        db = MySQLdb.connect(host=database_config["host"],
        user=database_config["username"], passwd=database_config["password"], db=database_config["name"])
        cursor = db.cursor()
        cursor.execute("SELECT * FROM cloud_setting;")
        results = cursor.fetchone()
        print(results[2])
    elif (arg == "frequency"):
        db = MySQLdb.connect(host=database_config["host"],
        user=database_config["username"], passwd=database_config["password"], db=database_config["name"])
        cursor = db.cursor()
        cursor.execute("SELECT * FROM cloud_setting;")
        results = cursor.fetchone()
        print(results[1])
if __name__ == "__main__":
    main()