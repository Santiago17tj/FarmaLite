[Setup]
AppName=FarmaLite
AppVersion=1.2.0
AppPublisher=LuisTj
DefaultDirName={autopf}\FarmaLite
DefaultGroupName=FarmaLite
OutputDir=.\Output
OutputBaseFilename=FarmaLite_Setup_1.2
Compression=lzma2/ultra
SolidCompression=yes
ArchitecturesInstallIn64BitMode=x64
DisableProgramGroupPage=yes
PrivilegesRequired=admin
SetupIconFile=logo.ico

[Dirs]
; Crear carpetas en ProgramData con permisos de modificación para todos los usuarios
Name: "{commonappdata}\FarmaLite"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\data"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\backups"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\uploads"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\uploads\Logo"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\logs"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\cache"; Permissions: users-modify
Name: "{commonappdata}\FarmaLite\temp"; Permissions: users-modify

[Files]
; Copiar el ejecutable y librerías de PHP Desktop a Program Files
Source: "FarmaLite.exe"; DestDir: "{app}"; Flags: ignoreversion
Source: "*.dll"; DestDir: "{app}"; Flags: ignoreversion
Source: "*.pak"; DestDir: "{app}"; Flags: ignoreversion
Source: "*.bin"; DestDir: "{app}"; Flags: ignoreversion
Source: "*.dat"; DestDir: "{app}"; Flags: ignoreversion
Source: "settings.json"; DestDir: "{app}"; Flags: ignoreversion
Source: "logo.ico"; DestDir: "{app}"; Flags: ignoreversion

; Carpetas necesarias de PHP Desktop
Source: "locales\*"; DestDir: "{app}\locales"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "php\*"; DestDir: "{app}\php"; Flags: ignoreversion recursesubdirs createallsubdirs

; Aplicación web
Source: "www\*"; DestDir: "{app}\www"; Flags: ignoreversion recursesubdirs createallsubdirs

; Solo la primera vez: instalar la base de datos vacía original en ProgramData
Source: "www\database\farmacia_template.db"; DestDir: "{commonappdata}\FarmaLite\data"; DestName: "farmacia.db"; Flags: onlyifdoesntexist uninsneveruninstall

[Icons]
Name: "{autodesktop}\FarmaLite"; Filename: "{app}\FarmaLite.exe"; IconFilename: "{app}\logo.ico"
Name: "{group}\FarmaLite"; Filename: "{app}\FarmaLite.exe"; IconFilename: "{app}\logo.ico"
Name: "{group}\Desinstalar FarmaLite"; Filename: "{uninstallexe}"

[Code]
procedure CurUninstallStepChanged(CurUninstallStep: TUninstallStep);
var
  ResultCode: Integer;
begin
  if CurUninstallStep = usPostUninstall then
  begin
    if MsgBox('¿Desea conservar la información del negocio (base de datos, respaldos, logos)?' + #13#10#13#10 + 'Seleccione "Sí" para conservar sus datos (Recomendado).' + #13#10 + 'Seleccione "No" para ELIMINAR TODO de forma definitiva.', mbConfirmation, MB_YESNO) = idNo then
    begin
      DelTree(ExpandConstant('{commonappdata}\FarmaLite'), True, True, True);
    end;
  end;
end;
