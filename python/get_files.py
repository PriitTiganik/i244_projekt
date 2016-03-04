#!C:/Python27/python.exe

import sys
import os
import json


def get_home_file_paths(directory):
    dir_paths =[]
    for root, dirs, files in os.walk(directory):
        for name in dirs:
            dir_paths.append(os.path.join(root, name))

    return json.dumps(dir_paths)

#print get_home_file_paths("C:\Python27")

if __name__** "__main__":
    print get_home_file_paths(sys.argv[1])

