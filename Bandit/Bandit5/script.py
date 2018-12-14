import os, glob, pathlib, stat

for path, subdirs, files in os.walk("./"):
    for name in files:
        current = str(pathlib.PurePath(path, name))
        size = os.stat(current).st_size
        executable = os.access(current,os.X_OK)
        human_readable = str(open(current)).isalnum()
        if size == 1033 and not executable:
            print(open(current).read().rstrip())