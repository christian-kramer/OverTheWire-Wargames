import os, glob, pathlib, stat
from pwd import getpwuid
from pathlib import Path

for path, subdirs, files in os.walk("/"):
    for name in files:
        current = str(pathlib.PurePath(path, name))
        if Path(current).is_file():
            size = os.stat(current).st_size
            if size == 33:
                print(getpwuid(stat(current).st_uid).pw_name)