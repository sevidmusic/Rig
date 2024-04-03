#!/usr/bin/env sh

msg() {
	printf '%b%s%b' "\n\n\e[0m\e[105m\e[30m" "$1" "\e[0m\n\n"
}

msgAndExit() {
	msg "$1"
	exit 1
}

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

pathToSetupRigScript=$(dirname -- "$(rreadlink "$0")")
pathToRig="$pathToSetupRigScript/rig"
pathToRigSymLink=~/.local/bin/rig

[ -f "${pathToRigSymLink}" ] &&
	[ $# -eq 1 ] &&
	[ "$1" = "--force" ] &&
	command ln -sf "${pathToRig}" "${pathToRigSymLink}" &&
	msgAndExit "Forced creation of symlink to ${pathToRig} at ${pathToRigSymLink}"

[ ! -f "${pathToRigSymLink}" ] &&
	command ln -s "${pathToRig}" "${pathToRigSymLink}" &&
	msgAndExit "Created symlink to ${pathToRig} at ${pathToRigSymLink}"

msgAndExit "Failed to create symlink to ${pathToRig} at ${pathToRigSymLink}!"
