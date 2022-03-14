import time
from pathlib import PosixPath

import requests
from fabric import Connection, Config, task
from patchwork import files


def _get_latest_github_release(org, repo):
    """Return the latest release tag from GitHub"""
    r = requests.get(f"https://api.github.com/repos/{org}/{repo}/releases/latest")
    r.raise_for_status()
    return r.json()["tag_name"]


RELEASE = _get_latest_github_release("cluebotng", "api")
TOOL_DIR = PosixPath("/data/project/cluebotng-api")

c = Connection(
    "login.toolforge.org",
    config=Config(overrides={"sudo": {"user": "tools.cluebotng-api", "prefix": "/usr/bin/sudo -ni"}}),
)


def _setup():
    """Setup the core directory structure"""
    if not files.exists(c, f'{TOOL_DIR / "apps"}'):
        print("Creating apps path")
        c.sudo(f'mkdir -p {TOOL_DIR / "apps"}')

    release_dir = f'{TOOL_DIR / "apps" / "api"}'
    if not files.exists(c, release_dir):
        print("Cloning repo")
        c.sudo(f"git clone https://github.com/cluebotng/api.git {release_dir}")
        c.sudo(f'ln -sf {release_dir} {TOOL_DIR / "public_html"}')


def _stop():
    """Stop all k8s jobs."""
    print('Stopping k8s jobs')
    c.sudo('webservice stop | true')


def _start():
    """Start all k8s jobs."""
    print('Starting k8s jobs')
    c.sudo(f'cp -fv {TOOL_DIR / "apps" / "api" / "lighttpd.conf"} {TOOL_DIR}/.lighttpd.conf')
    c.sudo('webservice start --backend kubernetes')


def _update_api():
    """Update the api release."""
    print(f"Moving api to {RELEASE}")
    release_dir = TOOL_DIR / "apps" / "api"

    c.sudo(f"git -C {release_dir} reset --hard")
    c.sudo(f"git -C {release_dir} clean -fd")
    c.sudo(f"git -C {release_dir} fetch -a")
    c.sudo(f"git -C {release_dir} checkout {RELEASE}")

    c.sudo(f'{release_dir / "composer.phar"} self-update')
    c.sudo(f'{release_dir / "composer.phar"} install -d {release_dir}')


@task()
def restart(c):
    """Restart the k8s jobs, without changing releases."""
    _stop()
    _start()


@task()
def deploy(c):
    """Deploy the bot to the current release."""
    _setup()
    _update_api()
    restart(c)
