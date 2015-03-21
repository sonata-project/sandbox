#!/bin/bash
# This file is part of the Sonata Project package.
#
# (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.

# This script populates a git repository with the content of a working repository
# The idea is to include everything into one repository to avoid calling composer
# or any pre processing tools while deploying
#
# Setup:
#    mkdir build    # the path where the final build reposository will be located
#    git init build # initialize a git repository (can be a git fetch if a remote is available)
#    mkdir project  # the project to build
#
# The command need to be called like this:
#    bin/qa_build_git.sh . ../build 1.0.0 1.0.0-DEV
#
#    This command will update the build repository by switching to the 1.0.0-DEV and then creating
#    or switching to the 1.0.0 branch. Switching first to the 1.0.0-DEV will help to keep the repository
#    small as the diff will be small.
#
# Configuration
#    There is no configuration file, however you can tweak some commands to match your needs
#    Also, make sure all paths are configured properly (like, assets with no symlinks)
#
# Deploy can be done by using
#    git fetch
#    git checkout 1.0.0-DEV

function display_help {
    echo ""
    echo "----"
    echo "The command is $0 SOURCE_FOLDER TARGET_FOLDER SOURCE_BRANCH TARGET_BRANCH"
    echo ""
    echo ""
}

function exit_on_error {
    if [ $2 -ne 0 ]; then
        echo "ERR $1"
        exit $2
    fi
}

if [ -z "$1" ]; then
    echo "ERR: you need to provide the source folder"
    display_help
    exit 1
fi

if [ ! -d "$1" ]; then
    echo "ERR: the source folder does not exist"
    display_help
    exit 1
fi

if [ -z "$2" ]; then
    echo "ERR: you need to provide the target folder"
    display_help
    exit 1
fi

if [ ! -d "$2" ]; then
    echo "ERR: the target folder does not exist"
    echo "Create the repository first: git init $2"
    display_help
    exit 1
fi


SOURCE_BRANCH="master"
if [ ! -z "$4" ]; then
    SOURCE_BRANCH="$4"
fi

SOURCE_DIR=`realpath $1`
TARGET_DIR=`realpath $2`
TARGET_BRANCH=$3
COMMIT=`cd $SOURCE_DIR && git rev-parse HEAD`


echo "Parameters"
echo "-------------------------------------------------------------"
echo " > Source Directory: ${SOURCE_DIR}"
echo " > Target Directory: ${TARGET_DIR}"
echo " > From Branch:      ${SOURCE_BRANCH}"
echo " > To Branch:        ${TARGET_BRANCH}"
echo " > Commit:           ${COMMIT}"
echo "-------------------------------------------------------------"
echo ""

# 2. switch to the source branch
cd $TARGET_DIR

# 2.1 do some cleanup ...
git reset --hard
git clean -f

git checkout $SOURCE_BRANCH
exit_on_error "Fail to switch to the source branch: ${SOURCE_BRANCH}" $?

# 3.1 switch/create to the branch
git checkout -b $TARGET_BRANCH

if [ $? -ne 0 ]; then
    echo "WARN: Fail to create a new branch: {$TARGET_BRANCH}"

    git checkout $TARGET_BRANCH

    exit_on_error "ERR: Fail to checkout the branch: {$TARGET_BRANCH}" $?
fi

# 3.2 create the tar command to retrieve and ignore some file and copy to the target folder
rsync -av \
    --exclude=app/config/parameters.yml \
    --exclude=.gitignore \
    --exclude=.git \
    --exclude=app/logs \
    --exclude=app/cache \
    --exclude=build \
    --exclude=puppet \
    --exclude=.idea \
    --exclude=*.jar \
    --exclude=.build \
    --exclude=.DS_Store \
    --exclude=web/uploads \
    --delete \
    $SOURCE_DIR/ $TARGET_DIR

exit_on_error "Fail to copy fail" $?

### 4.1 commit new file to the branch
git add -A
exit_on_error "Fail to add file to git repository" $?

git commit -m "build from ${COMMIT}"
exit_on_error "Fail to commit to repository" $?

# 5. push the code
git push --all
exit_on_error "Unable to push the code to the remote repository" $?

echo ""
echo "done!"

exit 0