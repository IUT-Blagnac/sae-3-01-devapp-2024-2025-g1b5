package sae.appli ;

public class AppState {
    private static long pythonPID = -1;

    public static long getPythonPID() {
        return pythonPID;
    }

    public static void setPythonPID(long pid) {
        pythonPID = pid;
    }
}
