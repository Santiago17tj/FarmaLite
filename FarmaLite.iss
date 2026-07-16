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

; La base de datos se instala siempre pero sin sobreescribir datos existentes (ver [Code])
Source: "www\database\farmacia_template.db"; DestDir: "{commonappdata}\FarmaLite\data"; DestName: "farmacia_template.db"; Flags: ignoreversion uninsneveruninstall

[Icons]
Name: "{autodesktop}\FarmaLite"; Filename: "{app}\FarmaLite.exe"; IconFilename: "{app}\logo.ico"
Name: "{group}\FarmaLite"; Filename: "{app}\FarmaLite.exe"; IconFilename: "{app}\logo.ico"
Name: "{group}\Desinstalar FarmaLite"; Filename: "{uninstallexe}"

[Code]
// Al iniciar la instalación: preguntar si es instalación nueva o reinstalación
function InitializeSetup(): Boolean;
var
  DbPath: String;
  Answer: Integer;
begin
  Result := True;
  DbPath := ExpandConstant('{commonappdata}\FarmaLite\data\farmacia.db');
  if FileExists(DbPath) then
  begin
    Answer := MsgBox(
      'FarmaLite detectó una instalación previa.' + #13#10#13#10 +
      '¿Desea REINICIAR la base de datos con las credenciales originales?' + #13#10 + #13#10 +
      'Seleccione "Sí" para borrar la base de datos antigua e instalar con las credenciales correctas.' + #13#10 +
      'Seleccione "No" para conservar todos los datos existentes.',
      mbConfirmation, MB_YESNO
    );
    if Answer = idYes then
    begin
      DeleteFile(DbPath);
    end;
  end;
end;

// Después de instalar: si no existe farmacia.db, copiar la plantilla
procedure CurStepChanged(CurStep: TSetupStep);
var
  TemplatePath, DbPath: String;
begin
  if CurStep = ssPostInstall then
  begin
    DbPath     := ExpandConstant('{commonappdata}\FarmaLite\data\farmacia.db');
    TemplatePath := ExpandConstant('{commonappdata}\FarmaLite\data\farmacia_template.db');
    if not FileExists(DbPath) then
    begin
      FileCopy(TemplatePath, DbPath, False);
    end;
  end;
end;

// Al desinstalar: preguntar si borrar datos
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
