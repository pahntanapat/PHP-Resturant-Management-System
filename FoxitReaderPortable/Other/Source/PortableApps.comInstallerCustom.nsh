!macro CustomCodePreInstall
	${If} ${FileExists} "$INSTDIR\Data\settings\FoxitReaderPortable.reg"
	${AndIfNot} ${FileExists} "$INSTDIR\Data\settings\FoxitReaderPortable5.reg"
		CopyFiles /SILENT "$INSTDIR\App\DefaultData\settings\FoxitReaderPortable5.reg" "$INSTDIR\Data\settings"
	${EndIf}
	${If} ${FileExists} "$INSTDIR\Data\settings\FoxitReaderPortable.reg"
	${AndIfNot} ${FileExists} "$INSTDIR\Data\settings\FoxitReaderPortable6.reg"
		CopyFiles /SILENT "$INSTDIR\App\DefaultData\settings\FoxitReaderPortable6.reg" "$INSTDIR\Data\settings"
	${EndIf}
!macroend