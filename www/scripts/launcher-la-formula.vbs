' Abre LA FORMULA sin mostrar ventana negra de consola
Set shell = CreateObject("WScript.Shell")
scriptDir = CreateObject("Scripting.FileSystemObject").GetParentFolderName(WScript.ScriptFullName)
launcher = scriptDir & "\launcher-la-formula.bat"
shell.Run """" & launcher & """", 0, False
