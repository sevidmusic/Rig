#!/usr/bin/env sh

rreadlink() (

	target=$1 fname='' targetDir='' CDPATH=''
	{
		\unalias command
		\unset -f command
	} >/dev/null 2>&1
	[ -n "$ZSH_VERSION" ] && export options[POSIX_BUILTINS]=on

	while :; do
		[ -L "$target" ] || [ -e "$target" ] || {
			command printf '%s\n' "ERROR: '$target' does not exist." >&2
			return 1
		}
		command cd "$(command dirname -- "$target")" || {
			command printf '%s\n' "ERROR: '$target' exists, but is not accessible." >&2
			return 1
		}
		fname=$(command basename -- "$target")
		[ "$fname" = '/' ] && fname=''
		if [ -L "$fname" ]; then
			target=$(command ls -l "$fname")
			target=${target#* -> }
			continue
		fi
		break
	done
	targetDir=$(command pwd -P)
	if [ "$fname" = '.' ]; then
		command printf '%s\n' "${targetDir%/}"
	elif [ "$fname" = '..' ]; then
		command printf '%s\n' "$(command dirname -- "${targetDir}")"
	else
		command printf '%s\n' "${targetDir%/}/$fname"
	fi
)

pathToSetupScript=$(dirname -- "$(rreadlink "$0")")

[ -f ~/.local/bin/rig ] &&
	[ $# -eq 1 ] && [ "$1" = "--force" ] &&
	command rm ~/.local/bin/rig

[ ! -f ~/.local/bin/rig ] &&
	command ln -s "$pathToSetupScript/rig" ~/.local/bin/rig
